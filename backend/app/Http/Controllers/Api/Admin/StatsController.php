<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        protected StatsService $statsService
    ) {}

    public function publicStats(): JsonResponse
    {
        return ApiResponse::success($this->statsService->publicStats());
    }

    public function overview(): JsonResponse
    {
        return ApiResponse::success($this->statsService->overview());
    }

    public function charts(): JsonResponse
    {
        return ApiResponse::success($this->statsService->charts());
    }

    public function topKeywords(): JsonResponse
    {
        return ApiResponse::success($this->statsService->topKeywords());
    }
}
