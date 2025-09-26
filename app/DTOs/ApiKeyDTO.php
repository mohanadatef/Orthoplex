<?php
namespace App\DTOs;

class ApiKeyDTO {
    public $name;
    public $scopes;
    public $expires_in_days;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? null;
        $this->scopes = $data['scopes'] ?? [];
        $this->expires_in_days = $data['expires_in_days'] ?? null;
    }
}
