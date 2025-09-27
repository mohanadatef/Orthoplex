<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\TwoFactorRepositoryInterface;
use App\Models\User;

class TwoFactorRepository implements TwoFactorRepositoryInterface
{
    public function saveSecret(int $userId, string $secret): bool
    {
        return User::where('id',$userId)->update(['two_factor_secret'=>$secret]) > 0;
    }

    public function enable(int $userId): bool
    {
        return User::where('id',$userId)->update(['two_factor_enabled'=>true]) > 0;
    }

    public function disable(int $userId): bool
    {
        return User::where('id',$userId)->update(['two_factor_enabled'=>false]) > 0;
    }

    public function storeBackupCodes(int $userId, array $codes): bool
    {
        return User::where('id',$userId)->update(['two_factor_backup_codes'=>json_encode($codes)]) > 0;
    }

    public function getUser(int $userId): ?User
    {
        return User::find($userId);
    }
}
