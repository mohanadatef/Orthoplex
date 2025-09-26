<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationService
{
    public function create(int $orgId, string $email, string $role = 'member'): Invitation
    {
        return Invitation::create([
            'org_id' => $orgId,
            'email'  => $email,
            'role'   => $role,
            'token'  => Str::random(64),
        ]);
    }

    public function accept(string $token, int $userId): bool
    {
        $inv = Invitation::where('token',$token)->where('accepted',false)->first();
        if(!$inv) return false;

        \DB::table('org_user')->insert([
            'org_id' => $inv->org_id,
            'user_id'=> $userId,
            'role'   => $inv->role,
        ]);

        $inv->accepted = true;
        $inv->save();

        return true;
    }
}
