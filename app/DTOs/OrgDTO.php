<?php
namespace App\DTOs;

class OrgDTO {
    public $name;
    public $domain;
    public $settings;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? null;
        $this->domain = $data['domain'] ?? null;
        $this->settings = $data['settings'] ?? [];
    }
}
