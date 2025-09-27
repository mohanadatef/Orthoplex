<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class VerificationController extends Controller
{
    public function __construct(private VerificationService $service) {}

    /**
     * @OA\Get(
     *   path="/api/v1/email/verify/{id}/{hash}",
     *   tags={"Auth"},
     *   summary="Verify user email via signed link",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Email verified successfully"),
     *   @OA\Response(response=403, description="Invalid or expired link")
     * )
     */
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message'=>'Invalid or expired verification link.'],403);
        }

        $user = User::findOrFail($id);

        if (! $this->service->verify($user, $hash)) {
            return response()->json(['message'=>'Invalid verification data.'],403);
        }

        return response()->json(['message'=>'Email verified successfully.']);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/email/resend",
     *   tags={"Auth"},
     *   summary="Resend verification email",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", example="user@example.com")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Verification email resent"),
     *   @OA\Response(response=400, description="Email already verified"),
     *   @OA\Response(response=404, description="User not found")
     * )
     */
    public function resend(ResendVerificationRequest $request): JsonResponse
    {
        $user = User::where('email',$request->validated('email'))->first();

        if (! $user) {
            return response()->json(['message'=>'User not found'],404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message'=>'Email already verified'],400);
        }

        $this->service->resend($user);

        return response()->json(['message'=>'Verification email resent.']);
    }
}
