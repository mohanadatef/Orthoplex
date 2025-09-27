<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\MagicLinkController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrgController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\GDPRController;
use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\OrgProvisioningController;

Route::prefix('v1')
    ->middleware(['localization'])
    ->group(function () {

    /**
     * ========================
     * 🔐 Auth & Verification
     * ========================
     */
    Route::post('auth/register', [AuthController::class, 'register'])
        ->middleware(['idempotency','throttle:5,1','audit:register']);
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware(['idempotency','throttle:login','audit:login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout', [AuthController::class,'logout'])
            ->middleware('audit:logout');
        Route::get('auth/me', [AuthController::class,'me']);
    });

    Route::get('email/verify/{id}/{hash}', [VerificationController::class,'verify'])
        ->name('verification.verify');
    Route::post('email/resend', [VerificationController::class,'resend'])
        ->middleware(['throttle:5,1','audit:resend_verification']);

    /**
     * ========================
     * 🔑 Two Factor & Magic Link
     * ========================
     */
    Route::middleware('auth:api')->group(function () {
        Route::post('2fa/generate', [TwoFactorController::class,'generate']);
        Route::post('2fa/enable', [TwoFactorController::class,'enable']);
        Route::post('2fa/verify', [TwoFactorController::class,'verify']);
    });
    Route::post('2fa/verify-login', [TwoFactorController::class,'verifyAndLogin']);

    Route::post('magic-link/request', [MagicLinkController::class,'requestLink'])
        ->middleware(['throttle:5,1']);
    Route::get('magic-link/verify', [MagicLinkController::class,'verify']);

    /**
     * ========================
     * 👥 Users & Orgs
     * ========================
     */
    Route::middleware(['auth:api'])->group(function () {
        Route::get('users', [UserController::class,'index'])->middleware('can:users.read');
        Route::delete('users/{id}', [UserController::class,'destroy'])
            ->middleware(['can:users.delete','audit:delete_user']);
        Route::post('users/{id}/restore', [UserController::class,'restore'])
            ->middleware(['can:users.update','audit:restore_user']);
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::post('orgs', [OrgController::class,'store'])->middleware('can:orgs.create');
        Route::put('orgs/{org}', [OrgController::class,'update'])->middleware('can:orgs.update');
        Route::delete('orgs/{org}', [OrgController::class,'destroy'])->middleware('can:orgs.delete');
        Route::post('/orgs/provision', [OrgProvisioningController::class,'provision'])
            ->middleware(['api.key']);
    });

    /**
     * ========================
     * ✉️ Invitations
     * ========================
     */
    Route::middleware(['auth:api','audit:invitation'])->group(function () {
        Route::post('invitations/invite', [InvitationController::class,'invite'])
            ->middleware('can:users.invite');
        Route::post('invitations/accept', [InvitationController::class,'accept'])->middleware('audit:accept_invitation');
    });

    /**
     * ========================
     * ⚖️ GDPR
     * ========================
     */
    Route::middleware(['auth:api'])->group(function () {
        Route::post('gdpr/export', [GDPRController::class,'export'])->middleware('can:gdpr.export');
        Route::post('gdpr/delete-request', [GDPRController::class,'requestDelete'])
            ->middleware(['throttle:delete-requests','audit:delete_request']);
        Route::post('gdpr/delete/{id}/approve', [GDPRController::class,'approve'])
            ->middleware('can:gdpr.approve');
        Route::post('gdpr/delete/{id}/reject', [GDPRController::class,'reject'])
            ->middleware('can:gdpr.approve');
        Route::get('gdpr/export/download', [GDPRController::class,'download'])
            ->name('gdpr.export.download')
            ->middleware('signed');
    });

    /**
     * ========================
     * 🔐 API Keys
     * ========================
     */
    Route::middleware(['auth:api','role:admin'])->group(function () {
        Route::post('api-keys', [ApiKeyController::class,'store']);
        Route::post('api-keys/{id}/rotate', [ApiKeyController::class,'rotate']);
    });

    /**
     * ========================
     * 📊 Analytics
     * ========================
     */
    Route::middleware(['auth:api','can:analytics.read'])->group(function () {
        Route::get('users/top-logins', [AnalyticsController::class,'topLogins']);
        Route::get('users/inactive', [AnalyticsController::class,'inactive']);
    });

    /**
     * ========================
     * 📡 Webhooks
     * ========================
     */
    Route::post('webhooks/receive', [WebhookController::class,'receive']); // internal use
    Route::get('webhooks/status/{id}', [WebhookController::class,'status']);
});
