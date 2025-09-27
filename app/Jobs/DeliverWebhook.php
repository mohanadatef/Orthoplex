<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverWebhook implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $url, public array $payload, public string $secret) {}

    public $tries = 5;
    public $backoff = [10, 30, 60, 120, 300];

    public function handle(): void
    {
        $signature = hash_hmac('sha256', json_encode($this->payload), $this->secret);
        Http::withHeaders([
            'X-Webhook-Signature' => $signature,
        ])->post($this->url, $this->payload)->throw();
    }

    public function failed(): void
    {
        // push to DLQ or log
    }
}
