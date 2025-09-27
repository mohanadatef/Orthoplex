<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    public function users(Request $request)
    {
        $q = (string)$request->query('q','');
        $orgId = optional($request->user())->org_id;
        $results = User::search($q)->get()->filter(fn($u) => $u->org_id === $orgId)->values();
        return response()->json(['data'=>$results]);
    }
}
