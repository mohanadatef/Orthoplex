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
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\IdempotencyMiddleware::class,
            \App\Http\Middleware\AuditLogMiddleware::class,
            \App\Http\Middleware\ApiKeyMiddleware::class,

            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':global,60,1',

        ]);

        $middleware->alias([
            'localize' => \App\Http\Middleware\Localization::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'throttle.auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':auth,10,1',
            'throttle.heavy' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':heavy,5,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
