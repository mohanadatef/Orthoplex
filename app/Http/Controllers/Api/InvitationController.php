<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvitationService;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $service) {}

    public function invite(Request $request)
    {
        $this->authorize('invite', \App\Models\User::class);

        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:owner,admin,member,auditor',
            'org_id'=> 'required|integer|exists:orgs,id'
        ]);

        $inv = $this->service->create($request->org_id, $request->email, $request->role);

        return response()->json([
            'message'=>'Invitation created',
            'token'=>$inv->token
        ]);
    }

    public function accept(Request $request)
    {
        $request->validate(['token'=>'required|string']);
        $userId = $request->user()->id;

        if(!$this->service->accept($request->token,$userId)) {
            return response()->json(['message'=>'Invalid or expired invitation'],400);
        }

        return response()->json(['message'=>'Invitation accepted']);
    }
}
