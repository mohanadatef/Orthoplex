<?php
namespace App\Repositories;

use Domain\Repositories\UserRepository;
use Domain\DTOs\UserDTO;
use App\Models\User;

final class EloquentUserRepository implements UserRepository
{
    public function create(UserDTO $dto): UserDTO
    {
        $u = User::create($dto->toArray());
        return new UserDTO($u->id, $u->org_id, $u->name, $u->email);
    }

    public function find(int $id): ?UserDTO
    {
        $u = User::find($id);
        return $u ? new UserDTO($u->id, $u->org_id, $u->name, $u->email) : null;
    }

    public function update(int $id, array $patch): UserDTO
    {
        $u = User::findOrFail($id);
        $u->fill($patch)->save();
        return new UserDTO($u->id, $u->org_id, $u->name, $u->email);
    }
}
