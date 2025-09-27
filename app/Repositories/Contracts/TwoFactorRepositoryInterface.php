<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface TwoFactorRepositoryInterface
{
    public function saveSecret(int $userId, string $secret): bool;
    public function enable(int $userId): bool;
    public function disable(int $userId): bool;
    public function storeBackupCodes(int $userId, array $codes): bool;
    public function getUser(int $userId): ?User;
}
