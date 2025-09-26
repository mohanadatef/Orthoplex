<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\DTOs\UserDTO;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *   path="/api/v1/auth/register",
 *   tags={"Auth"},
 *   summary="Register user",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"name","email","password","password_confirmation"},
 *       @OA\Property(property="name", type="string"),
 *       @OA\Property(property="email", type="string"),
 *       @OA\Property(property="password", type="string"),
 *       @OA\Property(property="password_confirmation", type="string")
 *     )
 *   ),
 *   @OA\Response(response=201, description="Created")
 * )
 *
 * @OA\Post(
 *   path="/api/v1/auth/login",
 *   tags={"Auth"},
 *   summary="Login user and return JWT token",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string"),
 *       @OA\Property(property="password", type="string")
 *     )
 *   ),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=401, description="Unauthorized")
 * )
 */

class AuthController extends Controller {
    protected $authService;
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request) {
        $dto = new UserDTO($request->validated());
        $user = $this->authService->register($dto);
        return response()->json(['user' => $user], 201);
    }

    public function login(LoginRequest $request) {
        $result = $this->authService->login($request->email, $request->password);

        if (is_array($result) && isset($result['error']) && $result['error'] === 'email_not_verified') {
            return response()->json(['message' => __('Please verify your email before login.' )], 403);
        }

        if(isset($result['2fa_required'])){
            return response()->json(['message'=>'2FA required'], 403);
        }

        if (!$result) return response()->json(['message'=>__('messages.invalid_credentials')], 401);

        return response()->json(['token' => $result]);
    }

    public function logout(Request $request) {
        $this->authService->logout();
        return response()->json(['message' => 'Logged out']);
    }

    public function me() {
        $user = auth()->user();
        return response()->json($user);
    }
}
