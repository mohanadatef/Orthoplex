<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GDPRController;
use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\MagicLinkController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\AnalyticsController;


Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class,'register'])->middleware('idempotency');
    Route::post('auth/login', [AuthController::class,'login'])->middleware(['throttle:login','idempotency','audit:login']);
    Route::middleware(['auth:api'])->group(function(){
        Route::post('auth/logout', [AuthController::class,'logout']);
        Route::get('auth/me', [AuthController::class,'me']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    });

    Route::get('email/verify', [VerificationController::class,'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->middleware('throttle:5,1');

    Route::middleware(['auth:api','role:admin','throttle:api'])->group(function(){
        Route::post('api-keys', [ApiKeyController::class,'store']);
        Route::post('api-keys/{id}/rotate', [ApiKeyController::class,'rotate']);
        Route::post('gdpr/export', [GDPRController::class,'export']);
    });
    Route::post('/gdpr/delete', [GDPRController::class,'requestDelete'])->middleware(['auth:api','audit:delete_request']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/gdpr/delete/{id}/approve', [GDPRController::class,'approve'])
            ->middleware('can:users.delete');
        Route::post('/gdpr/delete/{id}/reject', [GDPRController::class,'reject'])
            ->middleware('can:users.delete');
    });

    // GDPR
    Route::middleware('auth:api', 'throttle:delete-requests')->group(function(){
        Route::post('gdpr/delete-request', [GDPRController::class,'requestDelete']);
    });

    // Webhooks: open endpoints
    Route::post('webhooks/receive', [WebhookController::class,'receive']); // internal
    Route::get('webhooks/status/{id}', [WebhookController::class,'status']);

    Route::middleware('auth:api')->group(function(){
        Route::post('2fa/generate', [TwoFactorController::class,'generate']);
        Route::post('2fa/enable', [TwoFactorController::class,'enable']);
        Route::post('2fa/verify', [TwoFactorController::class,'verify']);
    });

    Route::post('magic-link/request',[MagicLinkController::class,'requestLink']);
    Route::get('magic-link/verify',[MagicLinkController::class,'verify']);
    Route::middleware(['auth:api','api.key'])->group(function(){
        Route::post('invitations/invite',[InvitationController::class,'invite']);
        Route::post('invitations/accept',[InvitationController::class,'accept']);
    });
    Route::get('/users/top-logins', [AnalyticsController::class, 'topLogins'])
        ->middleware(['auth:api','can:analytics.read']);
    Route::get('/users/inactive', [AnalyticsController::class, 'inactive'])
        ->middleware(['auth:api','can:analytics.read']);
});
