<?php
namespace App\Services\Contracts;
use App\DTOs\OrgDTO;

interface OrgServiceInterface {
    public function create(OrgDTO $dto);
}
