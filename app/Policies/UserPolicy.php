<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $authUser): bool
    {
        return $authUser->can('users.read');
    }

    public function update(User $authUser): bool
    {
        return $authUser->can('users.update');
    }

    public function invite(User $authUser): bool
    {
        return $authUser->can('users.invite');
    }

    public function delete(User $authUser, User $target): bool
    {
        return $authUser->can('users.delete') && $authUser->id !== $target->id;
    }

    public function restore(User $authUser, User $target): bool
    {
        return $authUser->can('users.update');
    }
}
