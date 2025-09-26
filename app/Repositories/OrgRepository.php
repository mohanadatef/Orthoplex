<?php
namespace App\Repositories;
use App\Models\Org;

class OrgRepository {
    protected $model;
    public function __construct(Org $model) {
        $this->model = $model;
    }
    public function create(array $data): Org {
        return $this->model->create($data);
    }
    public function find(int $id): ?Org {
        return $this->model->find($id);
    }
    public function all() {
        return $this->model->all();
    }
}
