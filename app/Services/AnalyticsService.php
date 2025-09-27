<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;

final class AnalyticsService
{
    public function topLogins(User $actor, string $window): array
    {
        $days = $this->windowToDays($window);
        $sql = "
            SELECT u.id, u.name, SUM(ld.count) as total
            FROM login_daily ld
            JOIN users u ON u.id = ld.user_id
            WHERE ld.org_id = ? AND ld.date >= CURRENT_DATE - INTERVAL ? DAY
            GROUP BY u.id, u.name
            ORDER BY total DESC
            LIMIT 50
        ";
        return DB::select($sql, [$actor->org_id, $days]);
    }

    public function inactiveUsers(User $actor, string $window): array
    {
        $days = $this->windowToDays($window);
        $sql = "
            SELECT u.id, u.name, u.email
            FROM users u
            LEFT JOIN login_events e ON e.user_id = u.id AND e.occurred_at >= NOW() - INTERVAL ? DAY
            WHERE u.org_id = ? AND e.id IS NULL
            ORDER BY u.id
            LIMIT 100
        ";
        return DB::select($sql, [$days, $actor->org_id]);
    }

    private function windowToDays(string $w): int
    {
        return match ($w) {
            'hour' => 1, 'day' => 1, 'week' => 7, 'month' => 30, '30d' => 30, default => 7
        };
    }
}
