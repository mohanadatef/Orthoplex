<?php

namespace App\DTOs;

class VerifyLogin2FADTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $code
    ) {}
}
