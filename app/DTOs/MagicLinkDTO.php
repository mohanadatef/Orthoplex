<?php

namespace App\DTOs;

/**
 * Data Transfer Object for Magic Link
 */
class MagicLinkDTO
{
    public function __construct(
        public int $user_id,
        public string $token,
        public \DateTime $expires_at
    ) {}
}
