<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key');
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        if (!$key || !$signature) {
            return response()->json(['message'=>'API key and signature required'],401);
        }

        $apiKey = ApiKey::where('key',$key)->first();
        if (!$apiKey) {
            return response()->json(['message'=>'Invalid API key'],401);
        }

        // expiry check
        if ($apiKey->isExpired() && !$apiKey->inGracePeriod()) {
            return response()->json(['message'=>'API key expired'],401);
        }

        // HMAC check
        if (! app(\App\Services\ApiKeyService::class)->verifyHmac($apiKey,$signature,$payload)) {
            return response()->json(['message'=>'Invalid signature'],401);
        }

        return $next($request);
    }
}
