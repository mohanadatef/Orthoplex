<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $key = 'idempotency:' . sha1($request->getRequestUri() . $request->getContent());

            if (cache()->has($key)) {
                return response()->json(['message' => 'Duplicate request detected'], 409);
            }

            cache()->put($key, true, now()->addSeconds(30));
        }

        return $next($request);
    }
}
