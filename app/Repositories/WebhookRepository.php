<?php
namespace App\Repositories;
use App\Models\Webhook;

class WebhookRepository {
    protected $model;
    public function __construct(Webhook $model) {
        $this->model = $model;
    }
    public function create(array $data): Webhook {
        return $this->model->create($data);
    }
    public function findPending() {
        return $this->model->where('status','pending')->get();
    }
}
