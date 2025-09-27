<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $key = $request->header('Idempotency-Key');

            if (! $key) {
                return response()->json(['message' => 'Idempotency-Key header required'], 400);
            }

            if (Cache::has($key)) {
                return response()->json(['message' => 'Duplicate request'], 409);
            }

            Cache::put($key, true, now()->addMinutes(5));
        }

        return $next($request);
    }
}
