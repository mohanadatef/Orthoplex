<?php
namespace App\Http\Controllers\Api;

use App\DTOs\Enable2FADTO;
use App\DTOs\VerifyLogin2FADTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use App\Http\Requests\TwoFactorVerifyLoginRequest;
use App\Http\Requests\TwoFactorEnableRequest;
use OpenApi\Annotations as OA;
use PragmaRX\Google2FA\Google2FA;
/**
 * @OA\Post(
 *     path="/api/v1/2fa/generate",
 *     tags={"2FA"},
 *     summary="Generate 2FA secret and QR code",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Secret and QR URL returned"
 *     )
 * )
 */
/**
 * @OA\Post(
 *     path="/api/v1/2fa/enable",
 *     tags={"2FA"},
 *     summary="Enable 2FA after verifying TOTP code",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"code"},
 *             @OA\Property(property="code", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="2FA enabled and backup codes returned"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid code"
 *     )
 * )
 */
/**
 * @OA\Post(
 *     path="/api/v1/2fa/verify",
 *     tags={"2FA"},
 *     summary="Verify 2FA code for logged-in user",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"code"},
 *             @OA\Property(property="code", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="2FA code verified successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid code"
 *     )
 * )
 */
/**
 * @OA\Post(
 *     path="/api/v1/2fa/verify-login",
 *     tags={"2FA"},
 *     summary="Verify 2FA code after login step (stateless) to issue JWT",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","code"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="code", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="JWT token issued after successful 2FA verification"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid code"
 *     )
 * )
 */


class TwoFactorController extends Controller
{
    protected $service;
    protected $google2fa;

    public function __construct(TwoFactorService $service, Google2FA $google2fa)
    {
        $this->service = $service;
        $this->google2fa = $google2fa;
    }

    /**
     * Generate secret and return provisioning info (logged-in user)
     * GET or POST: returns secret + QR URL + otpauth URL
     */
    public function generate(Request $request)
    {
        $user = $request->user();
        $secret = $this->service->generateSecret($user->id);

        $otpAuthUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // return secret and provisioning QR url (client can render QR)
        return response()->json([
            'secret' => $secret,
            'qr_url' => $otpAuthUrl,
            'provisioning_uri' => "otpauth://totp/".urlencode(config('app.name')).":".urlencode($user->email)."?secret={$secret}&issuer=".urlencode(config('app.name'))
        ]);
    }

    /**
     * Enable 2FA after user scans QR and provides a valid TOTP code
     */
    public function enable(TwoFactorEnableRequest $request)
    {
        $user = $request->user();
        $dto = new Enable2FADTO($user->id, $request->code);

        $ok = $this->service->verifyCode($dto->userId, $dto->code);
        if (! $ok) {
            return response()->json(['message' => 'Invalid 2FA code'], 400);
        }

        $this->service->enable2FA($dto->userId);
        $backupCodes = $this->service->generateBackupCodes($dto->userId);

        return response()->json([
            'message' => '2FA enabled',
            'backup_codes' => $backupCodes
        ]);
    }

    /**
     * Verify 2FA for logged-in user (simple check)
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();

        $ok = $this->service->verifyCode($user->id, $request->code);
        if ($ok) return response()->json(['message' => '2FA ok']);
        return response()->json(['message' => 'Invalid 2FA code'], 400);
    }

    /**
     * Stateless verification after login step 1:
     * Client sends { user_id, code } and on success we issue JWT.
     */
    public function verifyAndLogin(TwoFactorVerifyLoginRequest $request)
    {
        $dto = new VerifyLogin2FADTO($request->user_id, $request->code);

        $ok = $this->service->verifyCode($dto->userId, $dto->code);
        if (! $ok) {
            return response()->json(['message' => 'Invalid 2FA code'], 400);
        }

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser(
            \App\Models\User::find($dto->userId)
        );

        return response()->json([
            'message' => '2FA verified successfully',
            'token' => $token
        ]);
    }
}
