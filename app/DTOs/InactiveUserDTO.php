<?php

namespace App\DTOs;

/**
 * DTO for inactive user details
 */
class InactiveUserDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public ?string $last_login
    ) {}
}
