<?php
namespace App\Repositories;
use App\Models\User;

class UserRepository implements UserRepositoryInterface {
    protected $model;
    public function __construct(User $model) {
        $this->model = $model;
    }

    public function create(array $data): User {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User {
        return $this->model->where('email', $email)->first();
    }

    public function find(int $id): ?User {
        return $this->model->find($id);
    }
}
