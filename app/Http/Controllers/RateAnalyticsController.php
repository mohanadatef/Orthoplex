<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RateAnalyticsController extends Controller
{
    public function perOrg(Request $request)
    {
        $orgId = $request->user()->org_id;
        $rows = DB::table('rate_counters')
            ->where('org_id',$orgId)
            ->where('date','>=', now()->subDays(7)->toDateString())
            ->orderBy('date','desc')->get();
        return response()->json(['data'=>$rows]);
    }
}
