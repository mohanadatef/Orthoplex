<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\InvitationService;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $svc) {}

    public function send(Request $request): JsonResponse
    {
        $request->validate(['email'=>'required|email']);
        $data = $this->svc->invite($request->user(), $request->string('email'));
        return response()->json($data, 201);
    }

    public function accept(Request $request): JsonResponse
    {
        $request->validate([
            'token'=>'required','name'=>'required|string|max:100','password'=>'required|string|min:8'
        ]);
        $user = $this->svc->accept($request->string('token'), $request->string('name'), $request->string('password'));
        return response()->json($user, 201);
    }
}
