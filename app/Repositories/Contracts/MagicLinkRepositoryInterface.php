<?php

namespace App\Repositories\Contracts;

use App\Models\MagicLink;

interface MagicLinkRepositoryInterface
{
    public function create(array $data): MagicLink;
    public function findByToken(string $token): ?MagicLink;
    public function markUsed(MagicLink $link): bool;
}
