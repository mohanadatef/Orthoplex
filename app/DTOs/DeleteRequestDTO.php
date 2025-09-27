<?php

namespace App\DTOs;

/**
 * DTO for DeleteRequest
 */
class DeleteRequestDTO
{
    public function __construct(
        public int $user_id,
        public string $status ,
        public ?string $reason = null
    ) {}
}
