<?php

namespace App\DTOs;

class Enable2FADTO
{
    public function __construct(
        public int    $userId,
        public string $code
    )
    {
    }
}
