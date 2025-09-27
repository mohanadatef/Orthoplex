<?php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportUserData implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);
        $payload = [
            'user' => $user->toArray(),
            'logins_today' => [], // place to aggregate
        ];
        $path = 'exports/user-'.$user->id.'.json';
        Storage::disk('local')->put($path, json_encode($payload, JSON_PRETTY_PRINT));
        // Mail::to($user->email)->queue(new \App\Mail\GdprExportReadyMail(storage_path('app/'.$path)));
    }
}
