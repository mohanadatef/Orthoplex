<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    public function query(): Builder;
    public function findById(int $id, bool $withTrashed = false): ?User;
    public function softDelete(User $user): bool;
    public function restore(User $user): bool;
}
