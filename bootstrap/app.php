<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \App\Http\Middleware\AuditLogMiddleware::class,
            \App\Http\Middleware\ApiKeyAuthMiddleware::class,
            \App\Http\Middleware\DeleteRequestsThrottle::class,
            \App\Http\Middleware\LoginThrottle::class,
            \App\Http\Middleware\IdempotencyMiddleware::class,
            \App\Http\Middleware\LocalizationMiddleware::class,


        ]);

        $middleware->alias([
            'idempotency'       => \App\Http\Middleware\IdempotencyMiddleware::class,
            'audit'             => \App\Http\Middleware\AuditLogMiddleware::class,
            'api.key'           => \App\Http\Middleware\ApiKeyAuthMiddleware::class,
            'delete-requests'   => \App\Http\Middleware\DeleteRequestsThrottle::class,
            'login.throttle'    => \App\Http\Middleware\LoginThrottle::class,
            'localization' => \App\Http\Middleware\LocalizationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
