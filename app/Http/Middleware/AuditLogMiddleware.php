<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\DTOs\AuditLogDTO;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function __construct(private AuditLogService $service) {}

    public function handle(Request $request, Closure $next, string $action): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $dto = new AuditLogDTO(
                userId: $request->user()->id,
                action: $action,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                metadata: [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]
            );
            $this->service->log($dto);
        }

        return $response;
    }
}
