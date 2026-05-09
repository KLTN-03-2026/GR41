<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Document;
use App\Models\SearchHistory;
use App\Models\Synonym;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public function expandedKeywords(string $rawQuery): array
    {
        $query = mb_strtolower(trim($rawQuery));
        if ($query === '') {
            return [];
        }

        return $this->expandSynonyms($query);
    }

    /**
     * @param  array<string, mixed>  $filters  category, year, year_from, year_to, language, tag, sort
     */
    public function search(string $query, array $filters, ?int $userId): LengthAwarePaginator
    {
        $originalQuery = $query;
        $queryTrimmed = mb_strtolower(trim($query));

        $builder = Document::query()->with(['category', 'tags'])->where('documents.status', 'published');

        $keywordsForSort = [];
        if ($queryTrimmed !== '') {
            $keywordsForSort = $this->expandSynonyms($queryTrimmed);
            $builder->where(function (Builder $q) use ($keywordsForSort): void {
                foreach ($keywordsForSort as $kw) {
                    $like = '%'.$kw.'%';
                    $q->orWhere(function (Builder $sub) use ($like): void {
                        $sub->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhere('author', 'like', $like);
                    });
                }
            });
        }

        if (! empty($filters['category'])) {
            $catId = (int) $filters['category'];
            $childIds = Category::query()
                ->where('parent_id', $catId)
                ->pluck('id')
                ->toArray();
            $categoryIds = array_merge([$catId], $childIds);
            $builder->whereIn('category_id', $categoryIds);
        }
        if (! empty($filters['year'])) {
            $builder->where('published_year', $filters['year']);
        }
        if (! empty($filters['year_from'])) {
            $builder->where('published_year', '>=', (int) $filters['year_from']);
        }
        if (! empty($filters['year_to'])) {
            $builder->where('published_year', '<=', (int) $filters['year_to']);
        }
        if (! empty($filters['language'])) {
            $builder->where('language', $filters['language']);
        }
        if (! empty($filters['tag'])) {
            $builder->whereHas('tags', fn (Builder $q) => $q->where('tags.id', $filters['tag']));
        }

        $sort = $filters['sort'] ?? 'relevance';
        match ($sort) {
            'newest' => $builder->orderByDesc('documents.created_at'),
            'popular' => $builder->orderByDesc('documents.view_count'),
            'rating' => $builder->withAvg('ratings as avg_rating', 'score')->orderByDesc('avg_rating'),
            default => $this->applyRelevanceOrder($builder, $keywordsForSort),
        };

        $perPage = (int) ($filters['per_page'] ?? 12);
        $paginator = $builder->paginate($perPage);

        if ($queryTrimmed !== '') {
            SearchHistory::create([
                'user_id' => $userId,
                'keyword' => mb_substr($originalQuery, 0, 255),
                'result_count' => $paginator->total(),
                'searched_at' => now(),
            ]);
        }

        return $paginator;
    }

    /**
     * @return list<string>
     */
    private function expandSynonyms(string $query): array
    {
        $parts = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $expanded = [];
        foreach ($parts as $part) {
            $expanded[] = mb_strtolower($part);
        }

        $synonyms = Synonym::where(function ($q) use ($expanded): void {
            foreach ($expanded as $kw) {
                $q->orWhere('keyword', $kw);
            }
        })->get();

        foreach ($synonyms as $syn) {
            foreach ($syn->synonyms as $s) {
                $expanded[] = mb_strtolower((string) $s);
            }
        }

        if ($expanded === []) {
            $expanded[] = $query;
        }

        return array_values(array_unique($expanded));
    }

    private function applyRelevanceOrder(Builder $builder, array $keywords): void
    {
        if ($keywords === []) {
            /* Không có từ khóa: bọc "relevance" = mới nhất (tránh ORDER BY (0) — MySQL báo lỗi cột 0) */
            $builder->orderByDesc('documents.created_at');

            return;
        }

        $cases = [];
        foreach ($keywords as $i => $kw) {
            $safe = str_replace("'", "''", $kw);
            $cases[] = "CASE WHEN LOWER(title) LIKE '%{$safe}%' THEN ".(100 - $i).' ELSE 0 END';
        }
        $sql = implode(' + ', $cases);
        $builder->orderByRaw("({$sql}) DESC")->orderByDesc('documents.created_at');
    }

    /**
     * @return array{suggestions: array<int, string>}
     */
    public function suggestions(string $prefix): array
    {
        $prefix = mb_strtolower(trim($prefix));
        $hist = SearchHistory::query()
            ->where('keyword', 'like', $prefix.'%')
            ->selectRaw('keyword, COUNT(*) as c')
            ->groupBy('keyword')
            ->orderByDesc('c')
            ->limit(8)
            ->pluck('keyword')
            ->all();

        $docs = Document::query()->where('status', 'published')
            ->where('title', 'like', $prefix.'%')
            ->orderByDesc('view_count')
            ->limit(8)
            ->pluck('title')
            ->all();

        $merged = array_values(array_unique(array_merge($hist, $docs)));

        return ['suggestions' => array_slice($merged, 0, 8)];
    }

    /**
     * @return array{trending: array<int, array{keyword: string, count: int}>}
     */
    public function trending(): array
    {
        $rows = SearchHistory::query()
            ->where('searched_at', '>=', now()->subDays(7))
            ->selectRaw('keyword, COUNT(*) as cnt')
            ->groupBy('keyword')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        return [
            'trending' => $rows->map(fn ($r) => ['keyword' => $r->keyword, 'count' => (int) $r->cnt])->all(),
        ];
    }

    /**
     * Suggest a correction when the searched term yields few/no results.
     *
     * Strategy:
     *   1. Primary corpus  — keywords from search_history that have historically
     *      returned at least one result (result_count > 0), ranked by total hits.
     *   2. Fallback corpus — document titles ordered by view_count, so a brand-new
     *      installation with no history can still suggest real document names.
     *
     * @return array{did_you_mean: ?string}
     */
    public function fuzzyMatch(string $query): array
    {
        $normalized = mb_strtolower(trim($query));
        if ($normalized === '') {
            return ['did_you_mean' => null];
        }

        // Primary: only keywords that have returned results before
        $historyCandidates = SearchHistory::query()
            ->where('result_count', '>', 0)
            ->selectRaw('keyword, SUM(result_count) as score')
            ->groupBy('keyword')
            ->orderByDesc('score')
            ->limit(500)
            ->pluck('keyword')
            ->all();

        $best = $this->closestCandidate($normalized, $historyCandidates);

        // Fallback: document titles (works even with empty history)
        if ($best === null) {
            $titleCandidates = Document::query()->where('status', 'published')
                ->orderByDesc('view_count')
                ->limit(300)
                ->pluck('title')
                ->all();

            $best = $this->closestCandidate($normalized, $titleCandidates);
        }

        return ['did_you_mean' => $best];
    }

    /**
     * Return the candidate string closest to $query within an adaptive Levenshtein
     * threshold, or null if none qualifies.
     *
     * Threshold: 1 char (≤3), 2 chars (≤6), 3 chars (≤10), 30 % of length (longer).
     */
    private function closestCandidate(string $query, array $candidates): ?string
    {
        $queryLen = mb_strlen($query);
        $maxDist = match (true) {
            $queryLen <= 3  => 1,
            $queryLen <= 6  => 2,
            $queryLen <= 10 => 3,
            default         => (int) floor($queryLen * 0.3),
        };

        $best = null;
        $bestDist = PHP_INT_MAX;

        foreach ($candidates as $candidate) {
            $lower = mb_strtolower((string) $candidate);
            if ($lower === $query) {
                continue; // exact match — no correction needed
            }
            $dist = $this->mbLevenshtein($query, $lower);
            if ($dist <= $maxDist && $dist < $bestDist) {
                $bestDist = $dist;
                $best = $candidate;
            }
        }

        return $best;
    }

    /**
     * Unicode-safe Levenshtein distance.
     * PHP's built-in levenshtein() operates on bytes, not characters, so it gives
     * wrong distances for multi-byte strings (e.g. Vietnamese).
     */
    private function mbLevenshtein(string $s1, string $s2): int
    {
        $chars1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $chars2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $len1 = count($chars1);
        $len2 = count($chars2);

        if ($len1 === 0) {
            return $len2;
        }
        if ($len2 === 0) {
            return $len1;
        }

        // Rolling single-row DP — O(len2) space
        $prev = range(0, $len2);
        for ($i = 1; $i <= $len1; $i++) {
            $curr = [$i];
            for ($j = 1; $j <= $len2; $j++) {
                $cost = $chars1[$i - 1] === $chars2[$j - 1] ? 0 : 1;
                $curr[$j] = min(
                    $prev[$j] + 1,        // deletion
                    $curr[$j - 1] + 1,    // insertion
                    $prev[$j - 1] + $cost // substitution
                );
            }
            $prev = $curr;
        }

        return $prev[$len2];
    }
}
