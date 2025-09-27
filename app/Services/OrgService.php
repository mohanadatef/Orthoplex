<?php

namespace App\Services;

use App\DTOs\OrgDTO;
use App\Repositories\Contracts\OrgRepositoryInterface;
use App\Models\Org;

class OrgService
{
    public function __construct(
        private readonly OrgRepositoryInterface $repository
    ) {}

    public function create(OrgDTO $dto): Org
    {
        return $this->repository->create([
            'name'           => $dto->name,
            'webhook_url'    => $dto->webhook_url,
            'webhook_secret' => $dto->webhook_secret,
        ]);
    }

    public function update(Org $org, OrgDTO $dto): bool
    {
        return $this->repository->update($org, [
            'name'           => $dto->name,
            'webhook_url'    => $dto->webhook_url,
            'webhook_secret' => $dto->webhook_secret,
        ]);
    }

    public function delete(Org $org): bool
    {
        return $this->repository->delete($org);
    }
}
