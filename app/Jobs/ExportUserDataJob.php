<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ExportUserDataJob implements ShouldQueue {
    use Dispatchable, Queueable;
    protected $userId;
    public function __construct(int $userId) {
        $this->userId = $userId;
    }
    public function handle() {
        $user = User::with(['orgs'])->find($this->userId);
        if (!$user) return;
        $export = [
            'user' => $user->toArray(),
            'orgs' => $user->orgs->toArray(),
        ];
        $filename = 'exports/user_' . $user->id . '_' . time() . '.json';
        Storage::disk('local')->put($filename, json_encode($export));
    }
}
