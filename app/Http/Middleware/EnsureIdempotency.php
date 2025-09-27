<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IdempotencyKey;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');
        if (!$key) return $next($request);

        $hash = hash('sha256', $request->getContent() ?: json_encode($request->all()));
        $entry = IdempotencyKey::where('key',$key)->first();
        if ($entry && $entry->request_hash === $hash) {
            return response()->json($entry->response_body ?? [], $entry->status_code ?? 200);
        }

        $response = $next($request);
        IdempotencyKey::updateOrCreate(
            ['key'=>$key],
            [
                'user_id' => optional($request->user())->id,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'request_hash' => $hash,
                'response_body' => json_decode($response->getContent(), true),
                'status_code' => $response->getStatusCode(),
            ]
        );
        return $response;
    }
}
