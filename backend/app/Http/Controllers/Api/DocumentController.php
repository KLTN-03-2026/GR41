<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentView;
use App\Models\Favorite;
use App\Models\Rating;
use App\Services\RecommendService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DocumentController extends Controller
{
    public function __construct(
        protected RecommendService $recommendService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 12), 50);
        $q = Document::query()->with(['category', 'tags'])->where('status', 'published');

        if ($request->filled('category_slug')) {
            $cat = \App\Models\Category::query()->where('slug', $request->query('category_slug'))->first();
            if ($cat) {
                $q->where('category_id', $cat->id);
            }
        }

        if ($request->filled('category')) {
            $q->where('category_id', $request->query('category'));
        }
        if ($request->filled('year')) {
            $q->where('published_year', $request->query('year'));
        }
        if ($request->filled('language')) {
            $q->where('language', $request->query('language'));
        }
        if ($request->filled('tag')) {
            $q->whereHas('tags', fn (Builder $t) => $t->where('tags.id', $request->query('tag')));
        }

        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'popular' => $q->orderByDesc('view_count'),
            'rating' => $q->withAvg('ratings as avg_rating', 'score')->orderByDesc('avg_rating'),
            default => $q->orderByDesc('created_at'),
        };

        $paginator = $q->paginate($perPage);

        return ApiResponse::paginate($paginator, DocumentResource::class);
    }

    public function featured(): JsonResponse
    {
        $docs = Document::query()->where('status', 'published')->where('is_featured', true)->with(['category', 'tags'])
            ->orderByDesc('created_at')->limit(5)->get();

        return ApiResponse::success(DocumentResource::collection($docs));
    }

    public function popular(): JsonResponse
    {
        $docs = $this->recommendService->popular()->load(['category', 'tags']);

        return ApiResponse::success(DocumentResource::collection($docs));
    }

    public function recent(): JsonResponse
    {
        $docs = $this->recommendService->newest()->load(['category', 'tags']);

        return ApiResponse::success(DocumentResource::collection($docs));
    }

    public function recommended(Request $request): JsonResponse
    {
        $user = $request->user();
        $docs = $this->recommendService->forUser($user->id)->load(['category', 'tags']);

        return ApiResponse::success(DocumentResource::collection($docs));
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $document = Document::query()->with(['category', 'tags', 'uploader'])
            ->with(['ratings' => fn ($r) => $r->with('user')->latest()])
            ->withCount('ratings')
            ->withAvg('ratings as avg_rating', 'score')
            ->where('status', 'published')
            ->where('slug', $slug)->firstOrFail();

        $userId = $request->user('sanctum')?->id;

        $document->increment('view_count');
        DocumentView::create([
            'user_id' => $userId,
            'document_id' => $document->id,
            'ip_address' => $request->ip() ?? '0.0.0.0',
            'viewed_at' => now(),
        ]);

        if ($userId) {
            Cache::forget('recommend.user.'.$userId);
        }

        return ApiResponse::success(new DocumentResource($document));
    }

    public function related(int $id): JsonResponse
    {
        $docs = $this->recommendService->related($id)->load(['category', 'tags']);

        return ApiResponse::success(DocumentResource::collection($docs));
    }

    public function download(Request $request, int $id): JsonResponse
    {
        $document = Document::query()->where('status', 'published')->findOrFail($id);
        $document->increment('download_count');
        Cache::increment('stats.downloads.'.now()->toDateString());

        return ApiResponse::success(['file_url' => $document->file_url]);
    }

    public function toggleFavorite(Request $request, int $id): JsonResponse
    {
        $document = Document::query()->where('status', 'published')->findOrFail($id);
        $user = $request->user();

        $fav = Favorite::query()->where('user_id', $user->id)->where('document_id', $document->id)->first();

        if ($fav) {
            $fav->delete();
            $message = 'Đã bỏ yêu thích';
            $favorited = false;
        } else {
            Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);
            $message = 'Đã thêm yêu thích';
            $favorited = true;
        }

        return ApiResponse::success(['is_favorited' => $favorited], $message);
    }

    public function rate(RateDocumentRequest $request, int $id): JsonResponse
    {
        $document = Document::query()->where('status', 'published')->findOrFail($id);
        $user = $request->user();

        Rating::updateOrCreate(
            ['user_id' => $user->id, 'document_id' => $document->id],
            ['score' => $request->validated('score'), 'comment' => $request->validated('comment')]
        );

        return ApiResponse::success(null, 'Đánh giá đã được lưu');
    }
}
