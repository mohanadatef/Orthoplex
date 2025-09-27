<?php

namespace App\DTOs;

/**
 * DTO for transferring user registration data
 */
class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}
