<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\GdprExport;
use App\Services\GDPRExportService;
use App\Notifications\GDPRExportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ExportUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle(GDPRExportService $service): void
    {
        $user = User::findOrFail($this->userId);

        [$disk, $path] = $service->buildZipForUser($user);

        $token = Str::random(64);
        $expiresAt = now()->addDay();

        $export = GdprExport::create([
            'user_id'    => $user->id,
            'disk'       => $disk,
            'path'       => $path,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'gdpr.export.download',
            $expiresAt,
            ['token' => $export->token]
        );

        $user->notify(new GDPRExportCompletedNotification($signedUrl));
    }
}
