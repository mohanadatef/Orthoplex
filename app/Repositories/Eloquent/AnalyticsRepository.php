<?php

namespace App\Repositories\Eloquent;

use App\Models\LoginEvent;
use App\Models\User;
use App\Repositories\Contracts\AnalyticsRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;

class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    public function getTopLogins(int $limit = 10): CursorPaginator
    {
        return LoginEvent::select('user_id')
            ->selectRaw('count(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->cursorPaginate($limit);
    }

    public function getInactiveUsers(int $days = 30, int $perPage = 15): CursorPaginator
    {
        return User::query()
            ->leftJoin('login_events', 'users.id', '=', 'login_events.user_id')
            ->select('users.id','users.email')
            ->selectRaw('MAX(login_events.created_at) as last_login')
            ->groupBy('users.id','users.email')
            ->havingRaw('last_login IS NULL OR last_login < DATE_SUB(NOW(), INTERVAL ? DAY)', [$days])
            ->orderBy('users.id')
            ->cursorPaginate($perPage);
    }
}
