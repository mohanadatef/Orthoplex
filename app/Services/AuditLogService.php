<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function log(?int $userId, string $action, array $metadata = []): void
    {
        AuditLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata'   => $metadata,
        ]);
    }
}
