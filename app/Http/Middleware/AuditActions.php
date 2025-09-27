<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditActions
{
    public function handle(Request $request, Closure $next, string $action = null)
    {
        $response = $next($request);
        $user = $request->user();
        AuditLog::create([
            'actor_id' => $user?->id,
            'action' => $action ?? ($request->route()?->getName() ?: $request->path()),
            'resource_type' => $request->route()?->getControllerClass() ?: null,
            'resource_id' => (string)($request->route('id') ?? ''),
            'meta' => ['ip'=>$request->ip(),'ua'=>$request->userAgent(),'status'=>$response->getStatusCode()],
        ]);
        return $response;
    }
}
