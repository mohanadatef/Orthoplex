<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;

class RetryFailedWebhooksJob implements ShouldQueue {
    use Dispatchable, Queueable;

    public function handle() {
        $webhooks = Webhook::where('status','failed')->where('attempts','<',5)
            ->where(function($q){
                $q->whereNull('next_attempt_at')->orWhere('next_attempt_at','<=',now());
            })->get();

        foreach ($webhooks as $webhook) {
            try {
                $res = Http::timeout(10)->post($webhook->url, $webhook->payload);
                $webhook->attempts += 1;
                if ($res->successful()) {
                    $webhook->status = 'delivered';
                    $webhook->last_error = null;
                } else {
                    $webhook->status = 'failed';
                    $webhook->last_error = $res->body();
                    $webhook->next_attempt_at = now()->addMinutes( (int) pow(2, $webhook->attempts) * 5 );
                }
            } catch (\Exception $e) {
                $webhook->attempts += 1;
                $webhook->status = 'failed';
                $webhook->last_error = $e->getMessage();
                $webhook->next_attempt_at = now()->addMinutes((int) pow(2, $webhook->attempts) * 5);
            }
            $webhook->save();
        }
    }
}
