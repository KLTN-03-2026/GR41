<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ChatbotIntent;
use App\Models\ChatbotLog;
use App\Models\Document;
use Illuminate\Support\Str;

class ChatbotService
{
    /**
     * @return array{answer: string, intent: string, matched_intent: string}
     */
    public function ask(string $question, ?int $userId = null): array
    {
        $q = mb_strtolower(trim($question));
        $intents = ChatbotIntent::where('is_active', true)->get();

        $best = null;
        $bestScore = 0;
        foreach ($intents as $intent) {
            $score = 0;
            foreach ($intent->keywords as $kw) {
                if ($kw !== '' && str_contains($q, mb_strtolower((string) $kw))) {
                    $score++;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $intent;
            }
        }

        if (! $best || $bestScore === 0) {
            $best = ChatbotIntent::where('intent_key', 'fallback')->firstOrFail();
        }

        $answer = $this->renderTemplate($best, $q);

        ChatbotLog::create([
            'user_id' => $userId,
            'matched_intent_id' => $best->id,
            'question' => $question,
            'answer' => $answer,
            'created_at' => now(),
        ]);

        return ['answer' => $answer, 'intent' => $best->intent_key, 'matched_intent' => $best->intent_key];
    }

    private function renderTemplate(ChatbotIntent $intent, string $q): string
    {
        $tpl = $intent->response_template;
        // Normalize literal \n sequences (may appear when template is saved via admin UI textarea)
        $tpl = str_replace('\n', "\n", $tpl);
        $ds = $intent->data_source;

        $popular = $this->placeholderPopularDocuments();
        $tpl = str_replace('{{popular_documents}}', $popular, $tpl);

        $tpl = str_replace('{{categories_list}}', $this->placeholderCategories(), $tpl);

        $tpl = str_replace('{{new_documents}}', $this->placeholderNewDocuments(), $tpl);

        if ($intent->intent_key === 'find_document') {
            $topic = $this->guessTopicFromQuestion($q);
            $docs = Document::query()
                ->where('status', 'published')
                ->where(function ($b) use ($topic): void {
                    $b->where('title', 'like', '%'.$topic.'%')
                        ->orWhere('description', 'like', '%'.$topic.'%')
                        ->orWhere('author', 'like', '%'.$topic.'%');
                })
                ->limit(5)
                ->get();

            $list = $docs->map(fn (Document $d) => '- '.$d->title)->implode("\n");
            $tpl = str_replace(
                ['{{count}}', '{{topic}}', '{{list}}'],
                [(string) $docs->count(), $topic, $list ?: 'Hiện chưa có kết quả phù hợp.'],
                $tpl
            );
        }

        if ($ds === 'documents.popular') {
            $tpl = str_replace('{{popular_documents}}', $popular, $tpl);
        }

        return $tpl;
    }

    private function placeholderPopularDocuments(): string
    {
        $docs = Document::query()
            ->where('status', 'published')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        return $docs->map(fn (Document $d, int $i) => ($i + 1).'. '.$d->title.' ('.$d->view_count.' lượt xem)')
            ->implode("\n");
    }

    private function placeholderCategories(): string
    {
        $cats = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        return $cats->map(function (Category $c) {
            $children = $c->children->pluck('name')->implode(', ');

            return '- '.$c->name.($children !== '' ? ': '.$children : '');
        })->implode("\n");
    }

    private function placeholderNewDocuments(): string
    {
        $docs = Document::query()
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $docs->map(fn (Document $d, int $i) => ($i + 1).'. '.$d->title)->implode("\n");
    }

    private function guessTopicFromQuestion(string $q): string
    {
        // Normalize Unicode so Vietnamese diacriticals match consistently (NFC)
        if (class_exists(\Normalizer::class)) {
            $q = \Normalizer::normalize($q, \Normalizer::NFC) ?: $q;
        }

        // Trailing noise words common in Vietnamese questions
        $noisePattern = '/\s*(không|vậy|nhé|nha|ạ|nhỉ|ơi|đi|chứ|thế|thôi|đây|đó)[?!.,\s]*$/iu';

        // Ordered from most specific to least specific
        $markers = ['tài liệu về ', 'sách về ', 'tìm ', 'về ', 'sách ', 'tài liệu '];

        foreach ($markers as $marker) {
            $normMarker = class_exists(\Normalizer::class)
                ? \Normalizer::normalize($marker, \Normalizer::NFC) ?: $marker
                : $marker;

            $pos = mb_stripos($q, $normMarker);   // case-insensitive
            if ($pos !== false) {
                $topic = trim(mb_substr($q, $pos + mb_strlen($normMarker)));
                $topic = preg_replace($noisePattern, '', $topic) ?? $topic;
                $topic = rtrim($topic, '?!., ');
                if ($topic !== '' && mb_strlen($topic) <= 80) {
                    return $topic;
                }
            }
        }

        // Last resort — return the trimmed question without trailing noise
        $cleaned = preg_replace($noisePattern, '', $q) ?? $q;

        return Str::limit(rtrim($cleaned, '?!., '), 40, '');
    }
}
