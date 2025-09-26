<?php
namespace App\Repositories;
use App\Models\ApiKey;

class ApiKeyRepository {
    protected $model;
    public function __construct(ApiKey $model) {
        $this->model = $model;
    }
    public function create(array $data): ApiKey {
        return $this->model->create($data);
    }
    public function findByKey(string $key): ?ApiKey {
        return $this->model->where('key',$key)->first();
    }
}
