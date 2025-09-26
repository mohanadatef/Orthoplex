<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller {
    public function topLogins()
    {
        $data = \DB::table('login_events')
            ->select('user_id', \DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    public function inactive()
    {
        $days = request()->input('days', 30);

        $data = \DB::table('users')
            ->leftJoin('login_events', 'users.id', '=', 'login_events.user_id')
            ->select('users.id','users.email', \DB::raw('max(login_events.created_at) as last_login'))
            ->groupBy('users.id','users.email')
            ->havingRaw('last_login IS NULL OR last_login < DATE_SUB(NOW(), INTERVAL ? DAY)', [$days])
            ->get();

        return response()->json($data);
    }
}
