<?php
namespace App\Services;
use App\Models\Webhook;
use App\Repositories\WebhookRepository;
use App\DTOs\WebhookDTO;
use Illuminate\Support\Facades\Http;

class WebhookService {
    protected $repos;
    public function __construct(WebhookRepository $repos) {
        $this->repos = $repos;
    }

    public function dispatch(WebhookDTO $dto) {
        $record = $this->repos->create([
            'url' => $dto->url,
            'event' => $dto->event,
            'payload' => $dto->payload,
            'status' => 'pending'
        ]);

        try {
            $res = Http::post($dto->url, $dto->payload);
            $record->attempts = 1;
            if ($res->successful()) {
                $record->status = 'delivered';
            } else {
                $record->status = 'failed';
                $record->last_error = $res->body();
                $record->next_attempt_at = now()->addMinutes(5);
            }
            $record->save();
        } catch (\Exception $e) {
            $record->status = 'failed';
            $record->last_error = $e->getMessage();
            $record->attempts = 1;
            $record->next_attempt_at = now()->addMinutes(5);
            $record->save();
        }
        return $record;
    }

    public function sendWebhook(Webhook $webhook, array $payload): void
    {
        $org = $webhook->org;
        $secret = $org->webhook_secret;

        $body = json_encode($payload);
        $signature = hash_hmac('sha256', $body, $secret);

        Http::withHeaders([
            'X-Signature' => $signature,
            'Content-Type' => 'application/json'
        ])->post($webhook->url, $payload);
    }
}
