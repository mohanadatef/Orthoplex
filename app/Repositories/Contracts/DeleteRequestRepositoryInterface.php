<?php

namespace App\Repositories\Contracts;

use App\Models\DeleteRequest;

interface DeleteRequestRepositoryInterface
{
    public function create(array $data): DeleteRequest;
    public function findById(int $id): ?DeleteRequest;
    public function update(DeleteRequest $request, array $data): bool;
}
