<?php

use App\Http\Controllers\Api\Admin\CategoryAdminController;
use App\Http\Controllers\Api\Admin\ChatbotIntentController;
use App\Http\Controllers\Api\Admin\DocumentAdminController;
use App\Http\Controllers\Api\Admin\NotificationAdminController;
use App\Http\Controllers\Api\Admin\ProposalAdminController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\Admin\TagAdminController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Controllers\Api\Teacher\ProposalController as TeacherProposalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

    Route::get('roles', [RoleController::class, 'index']);

    Route::get('documents', [DocumentController::class, 'index']);
    Route::get('documents/featured', [DocumentController::class, 'featured']);
    Route::get('documents/popular', [DocumentController::class, 'popular']);
    Route::get('documents/recent', [DocumentController::class, 'recent']);
    Route::middleware('auth:sanctum')->get('documents/recommended', [DocumentController::class, 'recommended']);
    Route::get('documents/{id}/related', [DocumentController::class, 'related'])->whereNumber('id');
    Route::get('documents/{slug}', [DocumentController::class, 'show']);

    Route::get('search', [SearchController::class, 'search']);
    Route::get('search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('search/trending', [SearchController::class, 'trending']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('tags', [TagController::class, 'index']);

    Route::get('stats', [StatsController::class, 'publicStats']);

    Route::post('chatbot/ask', [ChatbotController::class, 'ask']);
    Route::get('chatbot/suggestions', [ChatbotController::class, 'suggestions']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('documents/{id}/download', [DocumentController::class, 'download'])->whereNumber('id');
        Route::post('documents/{id}/favorite', [DocumentController::class, 'toggleFavorite'])->whereNumber('id');
        Route::post('documents/{id}/rate', [DocumentController::class, 'rate'])->whereNumber('id');

        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('profile/avatar', [ProfileController::class, 'avatar']);
        Route::post('profile/change-password', [ProfileController::class, 'changePassword']);
        Route::get('profile/favorites', [ProfileController::class, 'favorites']);
        Route::delete('profile/favorites/{documentId}', [ProfileController::class, 'removeFavorite'])->whereNumber('documentId');
        Route::get('profile/history', [ProfileController::class, 'history']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead'])->whereNumber('id');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('documents', [DocumentAdminController::class, 'index']);
        Route::get('documents/{id}', [DocumentAdminController::class, 'show'])->whereNumber('id');
        Route::post('documents', [DocumentAdminController::class, 'store']);
        Route::put('documents/{id}', [DocumentAdminController::class, 'update'])->whereNumber('id');
        Route::delete('documents/{id}', [DocumentAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('categories', [CategoryAdminController::class, 'index']);
        Route::post('categories', [CategoryAdminController::class, 'store']);
        Route::put('categories/{id}', [CategoryAdminController::class, 'update'])->whereNumber('id');
        Route::delete('categories/{id}', [CategoryAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('tags', [TagAdminController::class, 'index']);
        Route::post('tags', [TagAdminController::class, 'store']);
        Route::put('tags/{id}', [TagAdminController::class, 'update'])->whereNumber('id');
        Route::delete('tags/{id}', [TagAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('users', [UserAdminController::class, 'index']);
        Route::get('users/{id}', [UserAdminController::class, 'show'])->whereNumber('id');
        Route::post('users', [UserAdminController::class, 'store']);
        Route::put('users/{id}', [UserAdminController::class, 'update'])->whereNumber('id');
        Route::patch('users/{id}/status', [UserAdminController::class, 'patchStatus'])->whereNumber('id');
        Route::delete('users/{id}', [UserAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('chatbot/intents', [ChatbotIntentController::class, 'index']);
        Route::post('chatbot/intents', [ChatbotIntentController::class, 'store']);
        Route::put('chatbot/intents/{id}', [ChatbotIntentController::class, 'update'])->whereNumber('id');
        Route::patch('chatbot/intents/{id}/toggle', [ChatbotIntentController::class, 'toggle'])->whereNumber('id');
        Route::delete('chatbot/intents/{id}', [ChatbotIntentController::class, 'destroy'])->whereNumber('id');

        Route::get('chatbot/logs', [ChatbotIntentController::class, 'logs']);

        Route::post('notifications/broadcast', [NotificationAdminController::class, 'broadcast']);

        Route::get('stats/overview', [StatsController::class, 'overview']);
        Route::get('stats/charts', [StatsController::class, 'charts']);
        Route::get('stats/top-keywords', [StatsController::class, 'topKeywords']);

        Route::get('proposals/pending-count', [ProposalAdminController::class, 'pendingCount']);
        Route::get('proposals', [ProposalAdminController::class, 'index']);
        Route::get('proposals/{id}', [ProposalAdminController::class, 'show'])->whereNumber('id');
        Route::post('proposals/{id}/approve', [ProposalAdminController::class, 'approve'])->whereNumber('id');
        Route::post('proposals/{id}/reject', [ProposalAdminController::class, 'reject'])->whereNumber('id');
    });

    Route::middleware(['auth:sanctum', 'role:teacher'])->prefix('teacher')->group(function () {
        Route::get('proposals', [TeacherProposalController::class, 'index']);
        Route::post('proposals', [TeacherProposalController::class, 'store']);
        Route::delete('proposals/{id}', [TeacherProposalController::class, 'destroy'])->whereNumber('id');
    });
});
