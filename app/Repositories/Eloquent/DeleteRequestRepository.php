<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DeleteRequestRepositoryInterface;
use App\Models\DeleteRequest;

class DeleteRequestRepository implements DeleteRequestRepositoryInterface
{
    public function create(array $data): DeleteRequest
    {
        return DeleteRequest::create($data);
    }

    public function findById(int $id): ?DeleteRequest
    {
        return DeleteRequest::find($id);
    }

    public function update(DeleteRequest $request, array $data): bool
    {
        return $request->update($data);
    }
}

