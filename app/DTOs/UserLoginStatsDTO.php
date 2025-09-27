<?php

namespace App\DTOs;

/**
 * DTO for user login statistics
 */
class UserLoginStatsDTO
{
    public function __construct(
        public int $user_id,
        public int $total
    ) {}
}
