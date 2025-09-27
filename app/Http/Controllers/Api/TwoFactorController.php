<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use App\Http\Requests\TwoFactor\TwoFactorEnableRequest;
use App\Http\Requests\TwoFactor\TwoFactorVerifyLoginRequest;
use App\DTOs\Enable2FADTO;
use App\DTOs\VerifyLogin2FADTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use PragmaRX\Google2FA\Google2FA;
use Tymon\JWTAuth\Facades\JWTAuth;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $service,
        private readonly Google2FA $google2fa
    ) {}

    /**
     * @OA\Post(
     *   path="/api/v1/2fa/generate",
     *   tags={"2FA"},
     *   summary="Generate 2FA secret and QR code",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Secret and QR URL returned")
     * )
     */
    public function generate(Request $request): JsonResponse
    {
        $user = $request->user();
        $secret = $this->service->generateSecret($user->id);

        $otpAuthUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'qr_url' => $otpAuthUrl,
            'provisioning_uri' => "otpauth://totp/".urlencode(config('app.name')).":".urlencode($user->email)."?secret={$secret}&issuer=".urlencode(config('app.name'))
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/2fa/enable",
     *   tags={"2FA"},
     *   summary="Enable 2FA after verifying TOTP code",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"code"},
     *       @OA\Property(property="code", type="string", example="123456")
     *     )
     *   ),
     *   @OA\Response(response=200, description="2FA enabled and backup codes returned"),
     *   @OA\Response(response=400, description="Invalid code")
     * )
     */
    public function enable(TwoFactorEnableRequest $request): JsonResponse
    {
        $dto = new Enable2FADTO($request->user()->id, $request->validated('code'));

        if (! $this->service->verifyCode($dto->userId, $dto->code)) {
            return response()->json(['message' => 'Invalid 2FA code'], 400);
        }

        $this->service->enable2FA($dto->userId);
        $backupCodes = $this->service->generateBackupCodes($dto->userId);

        return response()->json(['message'=>'2FA enabled','backup_codes'=>$backupCodes]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/2fa/verify",
     *   tags={"2FA"},
     *   summary="Verify 2FA code for logged-in user",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"code"},
     *       @OA\Property(property="code", type="string", example="123456")
     *     )
     *   ),
     *   @OA\Response(response=200, description="2FA code verified successfully"),
     *   @OA\Response(response=400, description="Invalid code")
     * )
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['code'=>'required|string']);
        $user = $request->user();

        return $this->service->verifyCode($user->id,$request->code)
            ? response()->json(['message'=>'2FA ok'])
            : response()->json(['message'=>'Invalid 2FA code'],400);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/2fa/verify-login",
     *   tags={"2FA"},
     *   summary="Verify 2FA code after login step (stateless) to issue JWT",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"user_id","code"},
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="code", type="string", example="123456")
     *     )
     *   ),
     *   @OA\Response(response=200, description="JWT token issued after successful 2FA verification"),
     *   @OA\Response(response=400, description="Invalid code")
     * )
     */
    public function verifyAndLogin(TwoFactorVerifyLoginRequest $request): JsonResponse
    {
        $dto = new VerifyLogin2FADTO($request->validated('user_id'), $request->validated('code'));

        if (! $this->service->verifyCode($dto->userId, $dto->code)) {
            return response()->json(['message'=>'Invalid 2FA code'],400);
        }

        $token = JWTAuth::fromUser($this->service->getUser($dto->userId));

        return response()->json(['message'=>'2FA verified successfully','token'=>$token]);
    }
}
