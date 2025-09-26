<?php
namespace App\Services;
use App\Repositories\OrgRepository;
use App\DTOs\OrgDTO;

class OrgService {
    protected $repos;
    public function __construct(OrgRepository $repos) {
        $this->repos = $repos;
    }
    public function create(OrgDTO $dto) {
        return $this->repos->create([
            'name' => $dto->name,
            'domain' => $dto->domain,
            'settings' => $dto->settings
        ]);
    }
}
