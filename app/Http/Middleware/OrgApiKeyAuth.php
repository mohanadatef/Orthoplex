<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\OrgApiKeyService;

class OrgApiKeyAuth
{
    public function handle(Request $request, Closure $next, string $scope = null)
    {
        $raw = $request->header('X-Org-ApiKey');
        if (!$raw) abort(401, 'Missing API key');
        $rec = OrgApiKeyService::resolveRaw($raw);
        if (!$rec) abort(401, 'Invalid API key');

        if ($scope && (!is_array($rec->scopes) || !in_array($scope, $rec->scopes, true))) {
            abort(403, 'Insufficient scope');
        }

        $request->attributes->set('org_id', $rec->org_id);
        $rec->last_used_at = now(); $rec->save();
        return $next($request);
    }
}
