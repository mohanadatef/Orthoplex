<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $actor, User $subject): bool
    {
        return $actor->org_id === $subject->org_id;
    }
}
