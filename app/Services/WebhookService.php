<?php

namespace App\Services;

use App\DTOs\WebhookDTO;
use App\Repositories\Contracts\WebhookRepositoryInterface;
use App\Models\Webhook;
use App\Models\Org;
use App\Models\WebhookDlq;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    public function __construct(
        private readonly WebhookRepositoryInterface $repository
    ) {}

    public function receive(WebhookDTO $dto): Webhook
    {
        $webhook = $this->repository->create([
            'url'     => $dto->url,
            'event'   => $dto->event,
            'payload' => $dto->payload,
            'status'  => 'pending',
            'attempts'=> 0,
        ]);

        return $this->send($webhook);
    }

    public function send(Webhook $webhook): Webhook
    {
        $org = Org::find($webhook->org_id);

        $signature = $this->generateSignature($webhook->payload, $org?->webhook_secret);

        try {
            $res = Http::timeout(8)
                ->withHeaders(['X-Org-Signature' => "sha256={$signature}"])
                ->post($webhook->url, $webhook->payload);

            $webhook->attempts++;

            if ($res->successful()) {
                $update = ['status' => 'delivered'];
            } else {
                $update = $this->handleFailure($webhook, $res->body());
            }

            $this->repository->update($webhook, $update);
        } catch (\Throwable $e) {
            $this->repository->update(
                $webhook,
                $this->handleFailure($webhook, $e->getMessage())
            );
        }

        return $webhook->refresh();
    }

    public function getStatus(int $id): ?Webhook
    {
        return $this->repository->findById($id);
    }


    private function generateSignature(array $payload, ?string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret ?? '');
    }

    private function handleFailure(Webhook $webhook, string $error): array
    {
        if ($webhook->attempts >= 5) {
            WebhookDlq::create([
                'webhook_id'   => $webhook->id,
                'url'          => $webhook->url,
                'event'        => $webhook->event,
                'payload'      => $webhook->payload,
                'error_message'=> $error,
            ]);

            return [
                'status' => 'dead',
                'last_error' => $error,
            ];
        }

        return [
            'status' => 'failed',
            'last_error' => $error,
            'next_attempt_at' => now()->addMinutes(5 * $webhook->attempts),
        ];
    }
}
