<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResendVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Notifications\VerifyEmailNotification;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *   path="/api/v1/email/resend",
 *   tags={"Auth"},
 *   summary="Resend email verification link",
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

class VerificationController extends Controller
{
    // GET route for verifying (signed link)
    public function verify(Request $request)
    {
        // Validate signed URL
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        $userId = $request->route('id');
        $user = User::findOrFail($userId);

        // validate hash
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification data.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully. You can now login.'], 200);
    }

    // POST: resend verification email (requires authentication)
    public function resend(ResendVerificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $user->notify(new VerifyEmailNotification($user));

        return response()->json(['message' => 'Verification email resent.']);
    }
}
