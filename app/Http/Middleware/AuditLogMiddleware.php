<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AuditLogService;

class AuditLogMiddleware
{
    public function __construct(private AuditLogService $audit) {}

    public function handle($request, Closure $next, string $action)
    {
        $response = $next($request);

        if ($response->status() < 400) {
            $this->audit->log(
                optional($request->user())->id,
                $action,
                ['url' => $request->path(), 'method' => $request->method()]
            );
        }

        return $response;
    }
}
