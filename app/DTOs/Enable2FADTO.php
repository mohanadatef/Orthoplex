<?php

namespace App\DTOs;

class Enable2FADTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $code
    ) {}
}
