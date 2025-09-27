<?php

namespace App\DTOs;

class AuditLogDTO
{
    public function __construct(
        public int $userId,
        public string $action,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?array $metadata = []
    ) {}
}
