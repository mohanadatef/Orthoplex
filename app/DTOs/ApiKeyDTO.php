<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

/**
 * Data Transfer Object for ApiKey
 */
class ApiKeyDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $key,
        public array $scopes = [],
        public ?Carbon $expires_at = null,
        public ?Carbon $rotated_at = null,
    ) {}
}
