<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle($request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');
        if (!$key) {
            return response()->json(['message' => 'Idempotency-Key header is required'], 400);
        }

        $cacheKey = 'idempotency:' . $request->method() . ':' . $request->path() . ':' . $key;

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey), 200);
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($response->getStatusCode() === Response::HTTP_OK || $response->getStatusCode() === Response::HTTP_CREATED) {
            Cache::put($cacheKey, json_decode($response->getContent(), true), now()->addMinutes(5));
        }

        return $response;
    }
}
