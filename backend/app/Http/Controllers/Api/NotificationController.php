<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $paginator = $user->libraryNotifications()
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 15));

        $unread = $user->libraryNotifications()->where('is_read', false)->count();

        return ApiResponse::success([
            'items' => NotificationResource::collection(collect($paginator->items()))->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'unread_count' => $unread,
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $n = Notification::query()->where('user_id', $request->user()->id)->findOrFail($id);
        $n->update(['is_read' => true]);

        return ApiResponse::success(new NotificationResource($n));
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->libraryNotifications()->where('is_read', false)->update(['is_read' => true]);

        return ApiResponse::success(null, 'Đã đánh dấu đã đọc tất cả');
    }
}
