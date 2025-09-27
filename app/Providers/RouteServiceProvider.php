<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return [Limit::perMinute(10)->by(optional($request->user())->id ?: $request->ip())];
        });

        RateLimiter::for('sensitive', function (Request $request) {
            return [Limit::perMinute(30)->by(optional($request->user())->id ?: $request->ip())];
        });
    }
}
