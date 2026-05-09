<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|integer',
            'year' => 'nullable|integer',
            'year_from' => 'nullable|integer',
            'year_to' => 'nullable|integer',
            'language' => 'nullable|string|max:10',
            'tag' => 'nullable|integer',
            'sort' => 'nullable|string|in:relevance,newest,popular,rating',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $filters = [
            'category' => $request->query('category'),
            'year' => $request->query('year'),
            'year_from' => $request->query('year_from'),
            'year_to' => $request->query('year_to'),
            'language' => $request->query('language'),
            'tag' => $request->query('tag'),
            'sort' => $request->query('sort', 'relevance'),
            'per_page' => $request->query('per_page', 12),
        ];

        $queryString = (string) $request->query('q', '');
        $userId = $request->user('sanctum')?->id ?? $request->user()?->id;

        $paginator = $this->searchService->search(
            $queryString,
            $filters,
            $userId
        );

        $extra = [];
        $trimmedQ = trim($queryString);
        if ($paginator->total() < 3 && $trimmedQ !== '') {
            $extra['did_you_mean'] = $this->searchService->fuzzyMatch($queryString)['did_you_mean'];
        }

        $expandedKeywords = $trimmedQ !== ''
            ? $this->searchService->expandedKeywords($queryString)
            : [];

        return ApiResponse::success(array_merge(
            [
                'items' => DocumentResource::collection(collect($paginator->items()))->resolve(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
                'expanded_keywords' => $expandedKeywords,
            ],
            $extra
        ));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $request->validate(['q' => 'nullable|string|max:255']);

        $result = $this->searchService->suggestions((string) $request->query('q', ''));

        return ApiResponse::success($result);
    }

    public function trending(): JsonResponse
    {
        return ApiResponse::success($this->searchService->trending());
    }
}
