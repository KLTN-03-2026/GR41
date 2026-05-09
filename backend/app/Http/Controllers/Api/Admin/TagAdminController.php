<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tag::query()->orderBy('name');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->query('q').'%');
        }

        $perPage = min((int) $request->query('per_page', 20), 500);
        $paginator = $query->paginate($perPage);

        return ApiResponse::paginate($paginator, TagResource::class);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), null);

        $tag = Tag::create($data);

        return ApiResponse::success(new TagResource($tag), 'Đã tạo tag', 201);
    }

    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $tag = Tag::query()->findOrFail($id);
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $id);
        }

        $tag->update($data);

        return ApiResponse::success(new TagResource($tag->fresh()));
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base;
        $i = 1;
        while (Tag::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function destroy(int $id): JsonResponse
    {
        Tag::query()->findOrFail($id)->delete();

        return ApiResponse::success(null, 'Đã xóa tag');
    }
}
