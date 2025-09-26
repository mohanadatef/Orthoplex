<?php
namespace App\Repositories\Contracts;
use App\Models\ApiKey;

interface ApiKeyRepositoryInterface {
    public function create(array $data): ApiKey;
    public function findByKey(string $key): ?ApiKey;
}
