<?php

namespace App\Repositories\Contracts;

interface OrgRepositoryInterface
{
    public function getByUserId(int $userId);
    public function createWithOwner(array $data, int $userId);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
}
