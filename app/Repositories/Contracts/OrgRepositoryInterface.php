<?php

namespace App\Repositories\Contracts;

use App\Models\Org;

interface OrgRepositoryInterface
{
    public function create(array $data): Org;
    public function update(Org $org, array $data): bool;
    public function findById(int $id): ?Org;
    public function delete(Org $org): bool;
}
