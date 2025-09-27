<?php

namespace App\Repositories\Contracts;

use App\Models\ApiKey;

interface ApiKeyRepositoryInterface
{
    public function create(array $data): ApiKey;
    public function findById(int $id): ?ApiKey;
    public function update(ApiKey $apiKey, array $data): bool;
}
