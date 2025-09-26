<?php
namespace App\Repositories\Contracts;
use App\Models\Org;

interface OrgRepositoryInterface {
    public function create(array $data): Org;
    public function find(int $id): ?Org;
    public function all();
}
