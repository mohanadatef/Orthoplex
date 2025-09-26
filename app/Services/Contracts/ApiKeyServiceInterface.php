<?php
namespace App\Services\Contracts;
use App\DTOs\ApiKeyDTO;

interface ApiKeyServiceInterface {
    public function create(ApiKeyDTO $dto);
    public function rotate(int $id);
}
