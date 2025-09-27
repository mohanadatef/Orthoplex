<?php
namespace Domain\Repositories;

use Domain\DTOs\UserDTO;

interface UserRepository
{
    public function create(UserDTO $dto): UserDTO;
    public function find(int $id): ?UserDTO;
    public function update(int $id, array $patch): UserDTO;
}
