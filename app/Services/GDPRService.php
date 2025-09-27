<?php
namespace App\Services;

use App\Jobs\ExportUserData;
use App\Models\{User, GdprExport};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\GdprExportReadyMail;
use Illuminate\Support\Str;

final class GDPRService
{
    public function enqueueExport($actor, string $userId): void
    {
        // build export synchronously for simplicity; can be queued
        $user = User::findOrFail((int)$userId);
        $dir = 'exports/user-'.$user->id.'/';
        Storage::disk('local')->makeDirectory($dir);

        // write JSON files
        Storage::disk('local')->put($dir.'profile.json', json_encode($user->toArray(), JSON_PRETTY_PRINT));
        // TODO: add more datasets as needed (events, roles, etc.)

        // zip folder
        $zipPath = 'exports/user-'.$user->id.'.zip';
        $zip = new \ZipArchive();
        $full = storage_path('app/'.$zipPath);
        if ($zip->open($full, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $files = Storage::disk('local')->allFiles($dir);
            foreach ($files as $file) {
                $zip->addFile(storage_path('app/'.$file), basename($file));
            }
            $zip->close();
        }

        // generate one-time token
        $token = Str::random(48);
        $exp = GdprExport::create([
            'user_id' => $user->id,
            'token' => hash('sha256',$token),
            'path' => $zipPath,
            'available_until' => now()->addDays(3),
        ]);

        $downloadUrl = url('/api/gdpr/exports/'. $exp->token);
        Mail::to($user->email)->queue(new GdprExportReadyMail($downloadUrl));
    }

    public function requestDeletion($actor, string $userId): void
    {
        \DB::table('gdpr_deletion_requests')->insert([
            'user_id' => (int)$userId,
            'requested_by' => $actor->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
