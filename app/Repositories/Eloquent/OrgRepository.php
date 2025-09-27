<?php

namespace App\Repositories\Eloquent;

use App\Models\Org;
use App\Repositories\Contracts\OrgRepositoryInterface;

class OrgRepository implements OrgRepositoryInterface
{
    public function getByUserId(int $userId)
    {
        return Org::whereHas('users', fn($q) => $q->where('user_id', $userId))->get();
    }

    public function createWithOwner(array $data, int $userId)
    {
        $org = Org::create($data);
        $org->users()->attach($userId, ['role' => 'owner']);
        return $org;
    }

    public function update(int $id, array $data)
    {
        $org = Org::find($id);
        if (! $org) return null;

        $org->update($data);
        return $org;
    }

    public function delete(int $id): bool
    {
        $org = Org::find($id);
        return $org ? (bool) $org->delete() : false;
    }
}
