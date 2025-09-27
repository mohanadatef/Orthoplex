<?php

namespace App\Repositories\Eloquent;

use App\Models\ApiKey;
use App\Repositories\Contracts\ApiKeyRepositoryInterface;

class ApiKeyRepository implements ApiKeyRepositoryInterface
{
    public function create(array $data): ApiKey
    {
        return ApiKey::create($data);
    }

    public function findById(int $id): ?ApiKey
    {
        return ApiKey::find($id);
    }

    public function update(ApiKey $apiKey, array $data): bool
    {
        return $apiKey->update($data);
    }
}
