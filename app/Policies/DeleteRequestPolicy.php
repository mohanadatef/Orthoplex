<?php

namespace App\Policies;

use App\Models\User;

class DeleteRequestPolicy
{
    public function approve(User $authUser)
    {
        return $authUser->can('users.delete');
    }
}
