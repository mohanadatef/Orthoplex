<?php

namespace App\Services;

use App\Repositories\Contracts\AnalyticsRepositoryInterface;
use App\DTOs\UserLoginStatsDTO;
use App\DTOs\InactiveUserDTO;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function __construct(
        private readonly AnalyticsRepositoryInterface $repository
    ) {}

    /**
     * Get top logins wrapped in DTO
     */
    public function topLogins(int $limit = 10): Collection
    {
        $result = $this->repository->getTopLogins($limit);

        return collect($result->items())->map(fn($row) =>
        new UserLoginStatsDTO($row->user_id, $row->total)
        );
    }

    /**
     * Get inactive users wrapped in DTO
     */
    public function inactive(int $days = 30, int $perPage = 15): Collection
    {
        $result = $this->repository->getInactiveUsers($days, $perPage);

        return collect($result->items())->map(fn($row) =>
        new InactiveUserDTO($row->id, $row->email, $row->last_login)
        );
    }
}
