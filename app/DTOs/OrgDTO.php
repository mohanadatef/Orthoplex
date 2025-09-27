<?php

namespace App\DTOs;

class OrgDTO
{
    public function __construct(
        public string $name,
        public ?string $webhook_url = null,
        public ?string $webhook_secret = null
    ) {}
}
