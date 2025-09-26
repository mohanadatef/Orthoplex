<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MagicLinkService;
use App\Notifications\MagicLinkNotification;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MagicLinkController extends Controller
{
    public function __construct(private MagicLinkService $service) {}

    public function requestLink(Request $request)
    {
        $request->validate(['email'=>'required|email']);

        $user = User::where('email',$request->email)->first();
        if(!$user) return response()->json(['message'=>'User not found'],404);

        $link = $this->service->createLink($user);

        $url = url("/api/v1/magic-link/verify?token={$link->token}");
        $user->notify(new MagicLinkNotification($url));

        return response()->json(['message'=>'Magic link sent to your email']);
    }

    public function verify(Request $request)
    {
        $request->validate(['token'=>'required']);
        $link = $this->service->validateLink($request->token);
        if(!$link) return response()->json(['message'=>'Invalid or expired link'],400);

        $user = $link->user;
        $this->service->markUsed($link);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token'=>$token]);
    }
}
