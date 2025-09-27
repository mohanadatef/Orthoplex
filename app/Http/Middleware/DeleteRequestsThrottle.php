<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteRequestsThrottle
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->user()?->id;
        $key = "delete_requests:{$userId}";

        if (cache()->has($key)) {
            return response()->json(['message'=>'Too many delete requests, try later'],429);
        }

        cache()->put($key, true, now()->addMinutes(5));

        return $next($request);
    }
}
