<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatbotAskRequest;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {}

    public function ask(ChatbotAskRequest $request): JsonResponse
    {
        $result = $this->chatbotService->ask(
            $request->validated('question'),
            $request->user('sanctum')?->id  // public route — must specify guard explicitly
        );

        return ApiResponse::success($result);
    }

    public function suggestions(): JsonResponse
    {
        $samples = [
            'Tài liệu phổ biến',
            'Hướng dẫn mượn sách',
            'Liên hệ thư viện',
            'Giờ mở cửa',
            'Cách tìm kiếm',
        ];

        return ApiResponse::success(['questions' => $samples]);
    }
}
