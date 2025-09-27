<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RateMetrics
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $orgId = optional($request->user())->org_id ?? $request->attributes->get('org_id');
        $route = $request->route()?->getName() ?: $request->path();
        DB::statement('INSERT INTO rate_counters (org_id, route, date, count, created_at, updated_at) VALUES (?,?,?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE count = count + 1, updated_at = NOW()', [
            $orgId, $route, now()->toDateString(), 1
        ]);
        return $response;
    }
}
