<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Document::query()->with(['category', 'tags']);

        if ($request->filled('q')) {
            $kw = '%'.$request->query('q').'%';
            $q->where(function ($b) use ($kw): void {
                $b->where('title', 'like', $kw)->orWhere('author', 'like', $kw);
            });
        }

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('year')) {
            $q->where('published_year', $request->query('year'));
        }

        if ($request->filled('is_featured')) {
            $v = $request->query('is_featured');
            $bool = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($bool !== null) {
                $q->where('is_featured', $bool);
            }
        }

        $paginator = $q->orderByDesc('id')->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginate($paginator, DocumentResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $document = Document::query()->with(['category', 'tags', 'uploader'])->findOrFail($id);

        return ApiResponse::success(new DocumentResource($document));
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['uploaded_by'] = $request->user()->id;

        $document = Document::create($data);
        $document->tags()->sync($tags);

        return ApiResponse::success(new DocumentResource($document->load(['category', 'tags'])), 'Đã tạo tài liệu', 201);
    }

    public function update(UpdateDocumentRequest $request, int $id): JsonResponse
    {
        $document = Document::query()->findOrFail($id);
        $data = $request->validated();
        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        if (isset($data['title'])) {
            $data['slug'] = $this->uniqueSlug($data['title'], $document->id);
        }

        $document->update($data);

        if (is_array($tags)) {
            $document->tags()->sync($tags);
        }

        return ApiResponse::success(new DocumentResource($document->fresh()->load(['category', 'tags'])));
    }

    public function destroy(int $id): JsonResponse
    {
        Document::query()->findOrFail($id)->delete();

        return ApiResponse::success(null, 'Đã xóa tài liệu');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title, '-', 'vi') ?: 'tai-lieu';
        $slug = $base;
        $i = 1;
        while (Document::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return substr($slug, 0, 280);
    }
}
