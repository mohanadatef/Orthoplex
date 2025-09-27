<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;

class ApiKeyAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $providedKey = $request->header('X-API-Key');
        $signature   = $request->header('X-Signature');
        $timestamp   = $request->header('X-Timestamp');

        if (! $providedKey || ! $signature || ! $timestamp) {
            return response()->json(['message' => 'Missing API authentication headers'], 401);
        }

        $apiKey = ApiKey::where('key', $providedKey)->first();
        if (! $apiKey || $apiKey->isExpired()) {
            return response()->json(['message' => 'Invalid or expired API key'], 401);
        }

        $secret = $apiKey->org->webhook_secret ?? null;
        if (! $secret) {
            return response()->json(['message' => 'Org secret missing'], 401);
        }

        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
