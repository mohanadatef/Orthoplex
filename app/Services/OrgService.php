<?php

namespace App\Services;

use App\Repositories\Contracts\OrgRepositoryInterface;

class OrgService
{
    public function __construct(private OrgRepositoryInterface $repository) {}

    public function list(int $userId)
    {
        return $this->repository->getByUserId($userId);
    }

    public function create(array $data, int $userId)
    {
        return $this->repository->createWithOwner($data, $userId);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
