<?php
namespace App\Repositories\Contracts;
use App\Models\Webhook;

interface WebhookRepositoryInterface {
    public function create(array $data): Webhook;
    public function findPending();
}
