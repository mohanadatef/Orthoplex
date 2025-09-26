<?php
namespace App\Services\Contracts;
use App\DTOs\WebhookDTO;

interface WebhookServiceInterface {
    public function dispatch(WebhookDTO $dto);
}
