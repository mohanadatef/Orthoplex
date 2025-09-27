<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\MagicLinkRepositoryInterface;
use App\Models\MagicLink;

class MagicLinkRepository implements MagicLinkRepositoryInterface
{
    public function create(array $data): MagicLink
    {
        return MagicLink::create($data);
    }

    public function findByToken(string $token): ?MagicLink
    {
        return MagicLink::where('token',$token)
            ->whereNull('used_at')
            ->where('expires_at','>',now())
            ->first();
    }

    public function markUsed(MagicLink $link): bool
    {
        $link->used_at = now();
        return $link->save();
    }
}
