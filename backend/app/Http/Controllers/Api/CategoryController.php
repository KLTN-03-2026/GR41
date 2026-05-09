<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $parents = Category::query()->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return ApiResponse::success(CategoryResource::collection($parents));
    }
}
