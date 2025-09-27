<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrgProvisioningController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\GDPRController;
use App\Http\Controllers\WebhookController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware(['throttle:auth','idempotent'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:auth'])->name('auth.login');

    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('magic-link', [AuthController::class, 'magicLink'])->name('auth.magic');
    Route::get('magic-link/consume', [AuthController::class, 'consumeMagic'])->name('auth.magic.consume');
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('auth.verify');
    Route::post('2fa/enable', [AuthController::class, 'enable2FA'])->name('auth.2fa.enable');
    Route::post('2fa/verify', [AuthController::class, 'verify2FA'])->name('auth.2fa.verify');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('auth.logout');
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
    Route::get('users/top-logins', [AnalyticsController::class, 'topLogins']);
    Route::get('users/inactive', [AnalyticsController::class, 'inactive']);
    Route::get('users/{id}/export', [GDPRController::class, 'export']);
    Route::post('users/{id}/gdpr-delete', [GDPRController::class, 'requestDelete']);
});

Route::post('orgs/provision', [OrgProvisioningController::class, 'handle']); // inbound partner endpoint

// webhooks (outbound simulation endpoint for local testing)
Route::post('webhooks/deliver', [WebhookController::class, 'deliver']);


use App\Http\Controllers\InvitationController;

Route::middleware(['auth:api','throttle:sensitive','idempotent'])->group(function () {
    Route::post('invitations', [InvitationController::class, 'send'])->middleware('can:invites.send')->name('invites.send');
});
Route::post('invitations/accept', [InvitationController::class, 'accept'])->name('invites.accept');

// Apply audit to user modification routes

Route::middleware(['auth:api','throttle:sensitive','audit:users.modify'])->group(function () {
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
});


/**
 * RBAC protection via policies & permissions
 */
Route::middleware(['auth:api','throttle:sensitive','audit:users.modify','can:users.manage'])->group(function () {
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
});

Route::get('gdpr/exports/{token}', [\App\Http\Controllers\GdprDownloadController::class, 'download'])->name('gdpr.download');

Route::middleware(['auth:api']).get('search/users', [\App\Http\Controllers\SearchController::class, 'users'])->name('search.users');

Route::middleware(['auth:api']).get('analytics/rate', [\App\Http\Controllers\RateAnalyticsController::class, 'perOrg'])->name('analytics.rate');
