<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatchUserStatusRequest;
use App\Http\Requests\StoreUserAdminRequest;
use App\Http\Requests\UpdateUserAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = User::query()->with('role');

        if ($request->filled('role')) {
            $q->whereHas('role', fn ($r) => $r->where('slug', $request->query('role')));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }
        if ($request->filled('q')) {
            $kw = '%'.$request->query('q').'%';
            $q->where(function ($b) use ($kw): void {
                $b->where('name', 'like', $kw)->orWhere('email', 'like', $kw)->orWhere('student_code', 'like', $kw);
            });
        }

        $paginator = $q->orderByDesc('id')->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginate($paginator, UserResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::query()->with([
            'role',
            'favorites.document',
            'searchHistory',
            'ratings.document',
        ])->findOrFail($id);

        return ApiResponse::success([
            'user' => new UserResource($user),
            'favorites' => $user->favorites,
            'search_history' => $user->searchHistory,
            'ratings' => $user->ratings,
        ]);
    }

    public function store(StoreUserAdminRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['password_confirmation']);
        $user = User::create($data);
        $user->load('role');

        return ApiResponse::success(new UserResource($user), 'Đã tạo người dùng', 201);
    }

    public function update(UpdateUserAdminRequest $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $data = $request->validated();
        unset($data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->load('role');

        return ApiResponse::success(new UserResource($user));
    }

    public function patchStatus(PatchUserStatusRequest $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $user->update(['status' => $request->validated('status')]);
        $user->load('role');

        return ApiResponse::success(new UserResource($user));
    }

    public function destroy(int $id): JsonResponse
    {
        User::query()->findOrFail($id)->delete();

        return ApiResponse::success(null, 'Đã xóa người dùng');
    }
}
