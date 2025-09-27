<?php

namespace App\Services;

use App\DTOs\InvitationDTO;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(
        private readonly InvitationRepositoryInterface $repository
    ) {}

    public function create(int $orgId, string $email, string $role)
    {
        $dto = new InvitationDTO($orgId, $email, $role);

        return $this->repository->create([
            'org_id' => $dto->org_id,
            'email'  => $dto->email,
            'role'   => $dto->role,
            'token'  => Str::random(40),
            'status' => 'pending',
        ]);
    }

    public function accept(string $token, int $userId): bool
    {
        $inv = $this->repository->findByToken($token);

        if (! $inv || $inv->status !== 'pending') {
            return false;
        }

        $this->repository->markAccepted($inv, $userId);

        $inv->org->users()->attach($userId, ['role' => $inv->role]);

        return true;
    }
}
