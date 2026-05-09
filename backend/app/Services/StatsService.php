<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\Document;
use App\Models\DocumentView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsService
{
    /**
     * @return array{total_documents: int, total_users: int, total_downloads: int}
     */
    public function publicStats(): array
    {
        return Cache::remember('stats.public', now()->addMinutes(10), function () {
            return [
                'total_documents' => Document::query()->count(),
                'total_users' => User::query()->count(),
                'total_downloads' => (int) Document::query()->sum('download_count'),
            ];
        });
    }

    /**
     * @return array{total_documents: int, total_users: int, downloads_today: int, chatbot_questions_week: int}
     */
    public function overview(): array
    {
        $downloadsToday = (int) Cache::get('stats.downloads.'.now()->toDateString(), 0);

        return [
            'total_documents' => Document::query()->count(),
            'total_users' => User::query()->count(),
            'downloads_today' => $downloadsToday,
            'chatbot_questions_week' => ChatbotLog::query()->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * @return array{visits_30d: array<int, array{date: string, count: int}>, category_distribution: array<int, array{name: string, count: int}>, top_documents: array<int, array{title: string, views: int}>}
     */
    public function charts(): array
    {
        $start = now()->subDays(30)->startOfDay();

        $visits = DocumentView::query()
            ->where('viewed_at', '>=', $start)
            ->selectRaw('DATE(viewed_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $cats = Category::query()
            ->leftJoin('documents', 'categories.id', '=', 'documents.category_id')
            ->selectRaw('categories.name, COUNT(documents.id) as cnt')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('cnt')
            ->limit(12)
            ->get();

        $top = Document::query()
            ->orderByDesc('view_count')
            ->limit(10)
            ->get(['title', 'view_count']);

        return [
            'visits_30d' => $visits->map(fn ($r) => ['date' => (string) $r->d, 'count' => (int) $r->c])->values()->all(),
            'category_distribution' => $cats->map(fn ($r) => ['name' => $r->name, 'count' => (int) $r->cnt])->values()->all(),
            'top_documents' => $top->map(fn ($d) => ['title' => $d->title, 'views' => (int) $d->view_count])->all(),
        ];
    }

    /**
     * @return array{keywords: array<int, array{keyword: string, count: int}>}
     */
    public function topKeywords(): array
    {
        $rows = DB::table('search_history')
            ->where('searched_at', '>=', now()->subDays(7))
            ->selectRaw('keyword, COUNT(*) as cnt')
            ->groupBy('keyword')
            ->orderByDesc('cnt')
            ->limit(20)
            ->get();

        return [
            'keywords' => $rows->map(fn ($r) => ['keyword' => $r->keyword, 'count' => (int) $r->cnt])->all(),
        ];
    }
}
