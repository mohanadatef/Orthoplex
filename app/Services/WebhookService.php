<?php

namespace App\Services;

use App\DTOs\WebhookDTO;
use App\Repositories\Contracts\WebhookRepositoryInterface;
use Illuminate\Support\Facades\Http;
use App\Models\Webhook;

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
        ]);

        try {
            $res = Http::timeout(8)->post($webhook->url, $webhook->payload);
            $update = [
                'attempts' => 1,
                'status'   => $res->successful() ? 'delivered' : 'failed',
            ];
            if (! $res->successful()) {
                $update['last_error'] = $res->body();
                $update['next_attempt_at'] = now()->addMinutes(5);
            }
            $this->repository->update($webhook,$update);
        } catch (\Exception $e) {
            $this->repository->update($webhook,[
                'status' => 'failed',
                'last_error' => $e->getMessage(),
                'attempts' => 1,
                'next_attempt_at' => now()->addMinutes(5),
            ]);
        }

        return $webhook->refresh();
    }

    public function getStatus(int $id): ?Webhook
    {
        return $this->repository->findById($id);
    }
}
