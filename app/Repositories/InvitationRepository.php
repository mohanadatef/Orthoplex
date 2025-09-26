<?php
namespace App\Repositories;
use App\Models\Invitation;

class InvitationRepository {
    protected $model;
    public function __construct(Invitation $model) {
        $this->model = $model;
    }
    public function create(array $data): Invitation {
        return $this->model->create($data);
    }
    public function findByToken(string $token): ?Invitation {
        return $this->model->where('token', $token)->first();
    }
}
