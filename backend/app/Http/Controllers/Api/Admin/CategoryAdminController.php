<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::query()->with(['parent']);

        if ($request->filled('q')) {
            $kw = '%'.$request->query('q').'%';
            $query->where('name', 'like', $kw);
        }

        $perPage = max(1, min((int) $request->query('per_page', 500), 500));

        $paginator = $query->orderBy('sort_order')->orderBy('name')->paginate($perPage);

        return ApiResponse::paginate($paginator, CategoryResource::class);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), null);

        $category = Category::create($data);

        return ApiResponse::success(new CategoryResource($category->load('children')), 'Đã tạo danh mục', 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::query()->findOrFail($id);
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $id);
        }

        $category->update($data);

        return ApiResponse::success(new CategoryResource($category->fresh()->load('children')));
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base;
        $i = 1;
        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        if ($category->children()->exists()) {
            return ApiResponse::error('Không thể xóa danh mục đang có danh mục con.', 422);
        }

        try {
            $category->delete();
        } catch (\Throwable $e) {
            return ApiResponse::error('Không thể xóa danh mục đang được sử dụng.', 422);
        }

        return ApiResponse::success(null, 'Đã xóa danh mục');
    }
}
