<?php

namespace App\Services;

use App\DTOs\MagicLinkDTO;
use App\Repositories\Contracts\MagicLinkRepositoryInterface;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MagicLinkService
{
    public function __construct(
        private readonly MagicLinkRepositoryInterface $repository
    ) {}

    public function createLink($user)
    {
        $dto = new MagicLinkDTO(
            $user->id,
            Str::random(60),
            Carbon::now()->addMinutes(15) // expires after 15 minutes
        );

        return $this->repository->create([
            'user_id'    => $dto->user_id,
            'token'      => $dto->token,
            'expires_at' => $dto->expires_at,
        ]);
    }

    public function validateLink(string $token)
    {
        return $this->repository->findByToken($token);
    }

    public function markUsed($link)
    {
        return $this->repository->markUsed($link);
    }
}
