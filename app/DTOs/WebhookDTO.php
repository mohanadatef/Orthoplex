<?php

namespace App\DTOs;

class WebhookDTO
{
    public function __construct(
        public string $url,
        public string $event,
        public array $payload
    ) {}
}
