<?php
namespace App\DTOs;
class UserDTO {
    public $name;
    public $email;
    public $password;
    public $locale;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->locale = $data['locale'] ?? 'en';
    }
}
