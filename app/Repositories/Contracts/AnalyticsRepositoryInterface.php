<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\CursorPaginator;

interface AnalyticsRepositoryInterface
{
    public function getTopLogins(int $limit = 10): CursorPaginator;
    public function getInactiveUsers(int $days = 30, int $perPage = 15): CursorPaginator;
}
