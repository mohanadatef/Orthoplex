<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryInterface
{
    public function query(): Builder
    {
        return User::query();
    }

    public function findById(int $id, bool $withTrashed = false): ?User
    {
        return $withTrashed ? User::withTrashed()->find($id) : User::find($id);
    }

    public function softDelete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }
}
