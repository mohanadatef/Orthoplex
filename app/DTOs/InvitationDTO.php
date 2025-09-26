<?php
namespace App\DTOs;

class InvitationDTO {
    public $org_id;
    public $email;

    public function __construct(array $data) {
        $this->org_id = $data['org_id'] ?? null;
        $this->email = $data['email'] ?? null;
    }
}
