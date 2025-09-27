<?php

namespace App\Services;

use App\DTOs\ApiKeyDTO;
use App\Repositories\Contracts\ApiKeyRepositoryInterface;
use Illuminate\Support\Carbon;
use App\Models\ApiKey;

class ApiKeyService
{
    public function __construct(
        private readonly ApiKeyRepositoryInterface $repository
    )
    {
    }

    public function create(string $name, ?array $scopes = [], ?int $expiresInDays = null): ApiKeyDTO
    {
        $apiKey = $this->repository->create([
            'name' => $name,
            'key' => ApiKey::generateKey(),
            'scopes' => $scopes,
            'expires_at' => $expiresInDays ? Carbon::now()->addDays($expiresInDays) : null,
        ]);

        return new ApiKeyDTO(
            $apiKey->id,
            $apiKey->name,
            $apiKey->key,
            $apiKey->scopes,
            $apiKey->expires_at,
            $apiKey->rotated_at
        );
    }

    public function rotate(int $id): ?ApiKeyDTO
    {
        $apiKey = $this->repository->findById($id);
        if (!$apiKey) {
            return null;
        }

        $oldKey = $apiKey->key;
        $apiKey->rotated_at = now();
        $apiKey->key = ApiKey::generateKey();
        $this->repository->update($apiKey, $apiKey->getAttributes());

        return new ApiKeyDTO(
            $apiKey->id,
            $apiKey->name,
            $apiKey->key,
            $apiKey->scopes,
            $apiKey->expires_at,
            $apiKey->rotated_at
        );
    }
}
