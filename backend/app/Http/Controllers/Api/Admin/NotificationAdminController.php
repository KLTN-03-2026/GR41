<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BroadcastNotificationRequest;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificationAdminController extends Controller
{
    public function broadcast(BroadcastNotificationRequest $request): JsonResponse
    {
        $target = $request->validated('target');
        $title = $request->validated('title');
        $content = $request->validated('content');

        $query = User::query()->where('status', 'active');
        $sentCount = 0;

        if ($target === 'students') {
            $sid = Role::where('slug', 'student')->value('id');
            $query->where('role_id', $sid);
        } elseif ($target === 'teachers') {
            $tid = Role::where('slug', 'teacher')->value('id');
            $query->where('role_id', $tid);
        }

        $query->chunkById(500, function ($users) use ($title, $content, &$sentCount): void {
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'content' => $content,
                    'type' => 'broadcast',
                    'is_read' => false,
                    'data' => null,
                ]);
                $sentCount++;
            }
        });

        return ApiResponse::success(['count' => $sentCount], "Đã gửi thông báo đến {$sentCount} người dùng");
    }
}
