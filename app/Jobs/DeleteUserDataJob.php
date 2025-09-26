<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteUserDataJob implements ShouldQueue {
    use Dispatchable, Queueable;

    public function handle()
    {
        $user = $this->user;
        $user->delete();
    }
}
