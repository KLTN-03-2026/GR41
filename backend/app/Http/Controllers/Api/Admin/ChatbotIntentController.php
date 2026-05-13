<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatbotIntentRequest;
use App\Http\Requests\UpdateChatbotIntentRequest;
use App\Http\Resources\ChatbotIntentResource;
use App\Http\Resources\ChatbotLogResource;
use App\Models\ChatbotIntent;
use App\Models\ChatbotLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotIntentController extends Controller
{
    public function index(): JsonResponse
    {
        $items = ChatbotIntent::query()->orderBy('intent_key')->get();

        return ApiResponse::success(ChatbotIntentResource::collection($items));
    }

    public function store(StoreChatbotIntentRequest $request): JsonResponse
    {
        $intent = ChatbotIntent::create($request->validated());

        return ApiResponse::success(new ChatbotIntentResource($intent), 'Đã tạo intent', 201);
    }

    public function update(UpdateChatbotIntentRequest $request, int $id): JsonResponse
    {
        $intent = ChatbotIntent::query()->findOrFail($id);
        $intent->update($request->validated());

        return ApiResponse::success(new ChatbotIntentResource($intent->fresh()));
    }

    public function toggle(int $id): JsonResponse
    {
        $intent = ChatbotIntent::query()->findOrFail($id);
        $intent->update(['is_active' => ! $intent->is_active]);

        return ApiResponse::success(new ChatbotIntentResource($intent->fresh()));
    }

    public function destroy(int $id): JsonResponse
    {
        ChatbotIntent::query()->findOrFail($id)->delete();

        return ApiResponse::success(null, 'Đã xóa intent');
    }

    public function logs(Request $request): JsonResponse
    {
        $request->validate([
            'intent_id' => 'nullable|integer|exists:chatbot_intents,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'date_to.after_or_equal' => 'Ngày bắt đầu phải <= ngày kết thúc.',
        ]);

        $q = ChatbotLog::query()->with(['intent', 'user']);

        if ($request->filled('intent_id')) {
            $q->where('matched_intent_id', $request->query('intent_id'));
        }
        $from = $request->query('date_from') ?? $request->query('from');
        $to = $request->query('date_to') ?? $request->query('to');
        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $paginator = $q->orderByDesc('created_at')->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginate($paginator, ChatbotLogResource::class);
    }
}
