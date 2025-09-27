<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\WebhookRepositoryInterface;
use App\Models\Webhook;

class WebhookRepository implements WebhookRepositoryInterface
{
    public function create(array $data): Webhook
    {
        return Webhook::create($data);
    }

    public function findById(int $id): ?Webhook
    {
        return Webhook::find($id);
    }

    public function update(Webhook $webhook, array $data): bool
    {
        return $webhook->update($data);
    }
}
