<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Notifications\VerifyEmailNotification;

class VerificationService
{
    public function verify(User $user, string $hash): bool
    {
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return true;
    }

    public function resend(User $user): void
    {
        $user->notify(new VerifyEmailNotification($user));
    }
}
