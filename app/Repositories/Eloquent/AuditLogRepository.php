<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Models\AuditLog;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function create(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}
