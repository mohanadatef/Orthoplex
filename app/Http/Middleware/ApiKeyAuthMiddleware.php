<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        if (! $key) {
            return response()->json(['message' => 'API key missing'], 401);
        }

        $apiKey = ApiKey::where('key',$key)->first();

        if (! $apiKey || ($apiKey->expires_at && $apiKey->expires_at->isPast())) {
            return response()->json(['message' => 'Invalid or expired API key'], 403);
        }

        return $next($request);
    }
}
