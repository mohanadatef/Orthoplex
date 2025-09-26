<?php

namespace App\Services;

use App\Models\MagicLink;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MagicLinkService
{
    public function createLink(User $user, int $ttlMinutes = 10): MagicLink
    {
        return MagicLink::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes)
        ]);
    }

    public function validateLink(string $token): ?MagicLink
    {
        $link = MagicLink::where('token',$token)->where('used',false)->first();
        if(!$link || $link->isExpired()) return null;

        return $link;
    }

    public function markUsed(MagicLink $link): void
    {
        $link->used = true;
        $link->save();
    }
}
