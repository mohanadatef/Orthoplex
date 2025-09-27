<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        AuditLog::create([
            'user_id'    => $this->user->id,
            'action'     => 'user_deleted',
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);

        \DB::transaction(function () {
            $this->user->tokens()->delete();
            $this->user->apiKeys()->delete();
            $this->user->loginEvents()->delete();
            $this->user->invitations()->delete();
            $this->user->delete();
        });

    }
}
