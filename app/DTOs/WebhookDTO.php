<?php
namespace App\DTOs;

class WebhookDTO {
    public $url;
    public $event;
    public $payload;

    public function __construct(array $data) {
        $this->url = $data['url'] ?? null;
        $this->event = $data['event'] ?? null;
        $this->payload = $data['payload'] ?? [];
    }
}
