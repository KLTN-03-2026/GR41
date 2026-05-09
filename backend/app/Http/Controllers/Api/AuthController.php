<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $studentRole = Role::where('slug', 'student')->firstOrFail();

        $user = User::create([
            'role_id' => $studentRole->id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'phone' => $request->validated('phone'),
            'student_code' => $request->validated('student_code'),
            'status' => 'active',
        ]);

        $user->load('role');

        return ApiResponse::success(['user' => new UserResource($user)], 'Đăng ký thành công', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [__('Thông tin đăng nhập không đúng.')],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        if ($user->status !== 'active') {
            Auth::logout();

            return ApiResponse::error(__('Tài khoản đã bị khóa.'), 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;
        $user->load('role');

        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::success(null, 'Đã đăng xuất');
    }

    public function me(Request $request): JsonResponse
    {
        $request->user()->load('role');

        return ApiResponse::success(new UserResource($request->user()));
    }

    /**
     * Tạo token đặt lại mật khẩu trên server, không gửi mail (EmailJS chỉ gọi được từ trình duyệt).
     * Trả `clientMail` { email, token } khi user tồn tại để SPA gửi mail; luôn 200 nếu không bị throttle.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $clientMail = null;

        $status = Password::sendResetLink($request->only('email'), function ($user, string $token) use (&$clientMail) {
            $clientMail = [
                'email' => $user->getEmailForPasswordReset(),
                'token' => $token,
            ];

            return Password::RESET_LINK_SENT;
        });

        if ($status === Password::RESET_THROTTLED) {
            return ApiResponse::error(__('Vui lòng đợi vài phút trước khi yêu cầu liên kết mới.'), 429);
        }

        return ApiResponse::success(
            ['clientMail' => $clientMail],
            __('Nếu email đã đăng ký, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.')
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ApiResponse::error(__('Đặt lại mật khẩu thất bại.'), 400);
        }

        return ApiResponse::success(null, __('Mật khẩu đã được cập nhật.'));
    }
}
