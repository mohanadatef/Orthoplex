<?php

namespace App\Services;

use App\DTOs\AuditLogDTO;
use App\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditLogService
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $repository
    ) {}

    public function log(AuditLogDTO $dto): void
    {
        $this->repository->create([
            'user_id'    => $dto->userId,
            'action'     => $dto->action,
            'ip_address' => $dto->ipAddress,
            'user_agent' => $dto->userAgent,
            'metadata'   => $dto->metadata ? json_encode($dto->metadata) : null,
            'created_at' => now(),
        ]);
    }
}
