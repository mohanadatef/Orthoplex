<?php
namespace App\Services;

use App\Jobs\DeliverWebhook;

final class WebhookService
{
    public function deliver(array $payload): void
    {
        $url = $payload['url'] ?? 'http://localhost:8025/collect';
        $secret = config('app.webhook_secret', env('WEBHOOK_HMAC_SECRET','dev-secret'));
        dispatch(new DeliverWebhook($url, $payload, $secret));
    }
}
