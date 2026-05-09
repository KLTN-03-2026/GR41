<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentView;
use Illuminate\Support\Facades\Cache;

class RecommendService
{
    public function forUser(int $userId)
    {
        return Cache::remember('recommend.user.'.$userId, now()->addMinutes(5), function () use ($userId) {
            $recentDocIds = DocumentView::query()
                ->where('user_id', $userId)
                ->orderByDesc('viewed_at')
                ->limit(10)
                ->pluck('document_id');

            if ($recentDocIds->isEmpty()) {
                return $this->popular();
            }

            $categories = Document::query()->whereIn('id', $recentDocIds)->pluck('category_id')->unique();

            return Document::query()
                ->where('status', 'published')
                ->whereIn('category_id', $categories)
                ->whereNotIn('id', $recentDocIds)
                ->orderByDesc('view_count')
                ->limit(8)
                ->get();
        });
    }

    public function related(int $documentId)
    {
        $doc = Document::query()->with('tags')->findOrFail($documentId);
        $tagIds = $doc->tags->pluck('id');

        return Document::query()
            ->where('documents.id', '!=', $documentId)
            ->where('status', 'published')
            ->where('category_id', $doc->category_id)
            ->when($tagIds->isNotEmpty(), fn ($q) => $q->whereHas('tags', fn ($tq) => $tq->whereIn('tags.id', $tagIds)))
            ->orderByDesc('view_count')
            ->limit(6)
            ->get();
    }

    public function popular()
    {
        return Document::query()->where('status', 'published')->orderByDesc('view_count')->limit(8)->get();
    }

    public function newest()
    {
        return Document::query()->where('status', 'published')->orderByDesc('created_at')->limit(8)->get();
    }
}
