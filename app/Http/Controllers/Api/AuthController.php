<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\DTOs\UserDTO;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *   path="/api/v1/auth/register",
     *   tags={"Auth"},
     *   summary="Register a new user",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password","password_confirmation"},
     *       @OA\Property(property="name", type="string", example="Mohanad Atef"),
     *       @OA\Property(property="email", type="string", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="secret123"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *     )
     *   ),
     *   @OA\Response(response=201, description="User registered")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new UserDTO(...$request->validated());
        $user = $this->authService->register($dto);

        return response()->json(['user' => $user], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/auth/login",
     *   tags={"Auth"},
     *   summary="Login user and return JWT token",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="secret123")
     *     )
     *   ),
     *   @OA\Response(response=200, description="JWT token returned"),
     *   @OA\Response(response=401, description="Invalid credentials"),
     *   @OA\Response(response=403, description="Email not verified or 2FA required")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->email, $request->password);

        if (is_array($result) && $result['error'] === 'email_not_verified') {
            return response()->json(['message' => __('Please verify your email.')], 403);
        }

        if (is_array($result) && isset($result['2fa_required'])) {
            return response()->json(['message' => '2FA required'], 403);
        }

        if (!$result) {
            return response()->json(['message' => __('messages.invalid_credentials')], 401);
        }

        return response()->json(['token' => $result]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/auth/logout",
     *   tags={"Auth"},
     *   summary="Logout user",
     *   security={{"bearerAuth": {}}},
     *   @OA\Response(response=200, description="Logged out"),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => 'Logged out']);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/auth/me",
     *   tags={"Auth"},
     *   summary="Get current authenticated user",
     *   security={{"bearerAuth": {}}},
     *   @OA\Response(response=200, description="Current user details"),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
}
