<?php

namespace App\Policies;

use App\Models\Org;
use App\Models\User;

class OrgPolicy
{
    public function view(User $authUser, Org $org): bool
    {
        return $authUser->hasRole('owner') || $authUser->hasRole('admin');
    }

    public function update(User $authUser, Org $org): bool
    {
        return $authUser->hasRole('owner');
    }

    public function delete(User $authUser, Org $org): bool
    {
        return $authUser->hasRole('owner');
    }
}
