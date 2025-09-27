<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Models\Invitation;

class InvitationRepository implements InvitationRepositoryInterface
{
    public function create(array $data): Invitation
    {
        return Invitation::create($data);
    }

    public function findByToken(string $token): ?Invitation
    {
        return Invitation::where('token', $token)->first();
    }

    public function markAccepted(Invitation $invitation, int $userId): bool
    {
        $invitation->accepted_at = now();
        $invitation->accepted_by = $userId;
        return $invitation->save();
    }
}
