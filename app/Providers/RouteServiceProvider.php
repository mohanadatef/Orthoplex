<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function ($request) {
            $email = (string) $request->input('email');
            $ip = $request->ip();

            return [
                Limit::perMinute(5)->by($email.$ip)->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again in 1 minute.'
                    ], 429);
                }),
            ];
        });
    }
}
