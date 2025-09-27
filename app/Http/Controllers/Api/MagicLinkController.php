<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MagicLink\RequestMagicLink;
use App\Http\Requests\MagicLink\VerifyMagicLink;
use App\Services\MagicLinkService;
use App\Notifications\MagicLinkNotification;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class MagicLinkController extends Controller
{
    public function __construct(private MagicLinkService $service) {}

    /**
     * @OA\Post(
     *   path="/api/v1/magic-link/request",
     *   tags={"Auth"},
     *   summary="Request a magic login link",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", example="user@example.com")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Magic link sent"),
     *   @OA\Response(response=404, description="User not found")
     * )
     */
    public function requestLink(RequestMagicLink $request): JsonResponse
    {
        $user = User::where('email',$request->validated('email'))->first();
        if (!$user) {
            return response()->json(['message'=>'User not found'],404);
        }

        $link = $this->service->createLink($user);

        $url = url("/api/v1/magic-link/verify?token={$link->token}");
        $user->notify(new MagicLinkNotification($url));

        return response()->json(['message'=>'Magic link sent to your email']);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/magic-link/verify",
     *   tags={"Auth"},
     *   summary="Verify magic link and return JWT",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", example="somerandomtoken123")
     *     )
     *   ),
     *   @OA\Response(response=200, description="JWT token issued"),
     *   @OA\Response(response=400, description="Invalid or expired link")
     * )
     */
    public function verify(VerifyMagicLink $request): JsonResponse
    {
        $link = $this->service->validateLink($request->validated('token'));
        if (!$link) {
            return response()->json(['message'=>'Invalid or expired link'],400);
        }

        $user = $link->user;
        $this->service->markUsed($link);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token'=>$token]);
    }
}
