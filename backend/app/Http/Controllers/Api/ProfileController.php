<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\UserResource;
use App\Models\DocumentView;
use App\Models\SearchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $request->user()->load('role');

        return ApiResponse::success(new UserResource($request->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        $request->user()->load('role');

        return ApiResponse::success(new UserResource($request->user()), 'Đã cập nhật hồ sơ');
    }

    public function avatar(AvatarRequest $request): JsonResponse
    {
        $request->user()->update(['avatar' => $request->validated('avatar')]);

        $request->user()->load('role');

        return ApiResponse::success(new UserResource($request->user()), 'Đã cập nhật ảnh đại diện');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! Hash::check($request->validated('current_password'), $user->password)) {
            return ApiResponse::error('Mật khẩu hiện tại không đúng.', 422);
        }

        $user->update(['password' => $request->validated('password')]);

        return ApiResponse::success(null, 'Đã đổi mật khẩu');
    }

    public function removeFavorite(Request $request, int $documentId): JsonResponse
    {
        $deleted = $request->user()->favorites()->where('document_id', $documentId)->delete();

        if ($deleted === 0) {
            return ApiResponse::error('Không tìm thấy tài liệu trong danh sách yêu thích.', 404);
        }

        return ApiResponse::success(null, 'Đã bỏ yêu thích');
    }

    public function favorites(Request $request): JsonResponse
    {
        $paginator = $request->user()
            ->favorites()
            ->with(['document.category', 'document.tags'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 12));

        $items = $paginator->getCollection()->map(fn ($f) => (new DocumentResource($f->document))->resolve())->values()->all();

        return ApiResponse::success([
            'items' => $items,
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
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $search_history = SearchHistory::query()->where('user_id', $userId)->orderByDesc('searched_at')->limit(50)->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'keyword' => $s->keyword,
                'query' => $s->keyword,
                'q' => $s->keyword,
                'result_count' => $s->result_count,
                'created_at' => $s->searched_at?->toIso8601String(),
            ]);

        $view_history = DocumentView::query()->where('user_id', $userId)->with('document:id,title,slug')->orderByDesc('viewed_at')->limit(50)->get()
            ->map(fn ($v) => [
                'document' => $v->document ? ['id' => $v->document->id, 'title' => $v->document->title, 'slug' => $v->document->slug] : null,
                'title' => $v->document?->title,
                'viewed_at' => $v->viewed_at?->toIso8601String(),
                'created_at' => $v->viewed_at?->toIso8601String(),
            ]);

        return ApiResponse::success([
            'search_history' => $search_history,
            'view_history' => $view_history,
        ]);
    }
}
