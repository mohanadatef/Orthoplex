<?php
namespace App\Services;

use App\Models\{Invitation, Org, User};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

final class InvitationService
{
    public function invite(User $actor, string $email): array
    {
        $token = Str::random(48);
        $inv = Invitation::create([
            'org_id' => $actor->org_id,
            'email' => $email,
            'token' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addDays(7),
        ]);
        // Mail::to($email)->queue(new \App\Mail\OrgInvitationMail($token)); // scaffold
        return ['token' => $token];
    }

    public function accept(string $rawToken, string $name, string $password): User
    {
        $token = hash('sha256', $rawToken);
        $inv = Invitation::where('token',$token)->whereNull('accepted_at')->firstOrFail();
        $inv->accepted_at = now();
        $inv->save();

        return User::create([
            'name' => $name,
            'email' => $inv->email,
            'password' => bcrypt($password),
            'org_id' => $inv->org_id,
            'email_verified_at' => now()
        ]);
    }
}
