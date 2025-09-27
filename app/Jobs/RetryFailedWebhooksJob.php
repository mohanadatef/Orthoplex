<?php

namespace App\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RetryFailedWebhooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $failed = Webhook::where('status', 'failed')
            ->where('next_attempt_at', '<=', now())
            ->limit(50)
            ->get();

        foreach ($failed as $webhook) {
            try {
                $res = Http::timeout(8)->post($webhook->url, $webhook->payload);

                if ($res->successful()) {
                    $webhook->update([
                        'status' => 'delivered',
                        'last_error' => null,
                    ]);
                } else {
                    $webhook->update([
                        'attempts' => $webhook->attempts + 1,
                        'last_error' => $res->body(),
                        'next_attempt_at' => now()->addMinutes(5),
                    ]);
                }
            } catch (\Exception $e) {
                $webhook->update([
                    'attempts' => $webhook->attempts + 1,
                    'last_error' => $e->getMessage(),
                    'next_attempt_at' => now()->addMinutes(5),
                ]);
            }
        }
    }
}
