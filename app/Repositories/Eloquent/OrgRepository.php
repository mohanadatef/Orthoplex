<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\OrgRepositoryInterface;
use App\Models\Org;

class OrgRepository implements OrgRepositoryInterface
{
    public function create(array $data): Org
    {
        return Org::create($data);
    }

    public function update(Org $org, array $data): bool
    {
        return $org->update($data);
    }

    public function findById(int $id): ?Org
    {
        return Org::find($id);
    }

    public function delete(Org $org): bool
    {
        return $org->delete();
    }
}
