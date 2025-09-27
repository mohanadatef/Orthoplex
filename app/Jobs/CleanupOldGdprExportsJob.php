<?php

namespace App\Jobs;

use App\Models\GdprExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CleanupOldGdprExportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        GdprExport::query()
            ->whereNotNull('downloaded_at')
            ->where('downloaded_at', '<', now()->subDays(2))
            ->each(function ($exp) {
                if (Storage::disk($exp->disk)->exists($exp->path)) {
                    Storage::disk($exp->disk)->delete($exp->path);
                }
                $exp->delete();
            });
    }
}
