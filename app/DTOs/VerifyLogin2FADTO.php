<?php

namespace App\DTOs;

class VerifyLogin2FADTO
{
    public function __construct(
        public int $userId,
        public string $code
    ) {}
}
