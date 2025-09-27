<?php

namespace App\Services;

use App\Repositories\Contracts\TwoFactorRepositoryInterface;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    public function __construct(
        private readonly TwoFactorRepositoryInterface $repository,
        private readonly Google2FA $google2fa
    ) {}

    public function generateSecret(int $userId): string
    {
        $secret = $this->google2fa->generateSecretKey();
        $this->repository->saveSecret($userId,$secret);
        return $secret;
    }

    public function verifyCode(int $userId, string $code): bool
    {
        $user = $this->repository->getUser($userId);
        if (! $user || ! $user->two_factor_secret) return false;

        return $this->google2fa->verifyKey($user->two_factor_secret, $code);
    }

    public function enable2FA(int $userId): void
    {
        $this->repository->enable($userId);
        $user = $this->repository->getUser($userId);
        $user->notify(new \App\Notifications\TwoFactorEnabledNotification());
    }

    public function disable2FA(int $userId): void
    {
        $this->repository->disable($userId);
        $user = $this->repository->getUser($userId);
        $user->notify(new \App\Notifications\TwoFactorDisabledNotification());

    }

    public function generateBackupCodes(int $userId): array
    {
        $codes = collect(range(1,8))->map(fn() => bin2hex(random_bytes(4)))->toArray();
        $this->repository->storeBackupCodes($userId, $codes);
        return $codes;
    }

    public function getUser(int $userId)
    {
        return $this->repository->getUser($userId);
    }

}
