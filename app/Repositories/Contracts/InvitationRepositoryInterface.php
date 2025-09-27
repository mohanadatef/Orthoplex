<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;

interface InvitationRepositoryInterface
{
    public function create(array $data): Invitation;
    public function findByToken(string $token): ?Invitation;
    public function markAccepted(Invitation $invitation, int $userId): bool;
}
