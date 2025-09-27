<?php

namespace App\DTOs;

/**
 * Data Transfer Object for Invitations
 */
class InvitationDTO
{
    public function __construct(
        public int $org_id,
        public string $email,
        public string $role
    ) {}
}
