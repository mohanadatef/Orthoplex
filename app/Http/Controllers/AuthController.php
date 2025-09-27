<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use App\Http\Requests\Auth\{LoginRequest, RegisterRequest, VerifyEmailRequest, MagicLinkRequest, Enable2FARequest, Verify2FARequest};

final class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->validated());
        return response()->json($result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->validated());
        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());
        return response()->json(['ok' => true]);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $this->auth->verifyEmail($request->validated());
        return response()->json(['ok' => true]);
    }

    public function magicLink(MagicLinkRequest $request): JsonResponse
    {
        $this->auth->sendMagicLink($request->validated());
        return response()->json(['ok' => true]);
    }

    public function consumeMagic(Request $request): JsonResponse
    {
        $result = $this->auth->consumeMagicLink((string)$request->query('token'));
        return response()->json($result);
    }

    public function enable2FA(Enable2FARequest $request): JsonResponse
    {
        $data = $this->auth->enable2FA($request->user());
        return response()->json($data);
    }

    public function verify2FA(Verify2FARequest $request): JsonResponse
    {
        $this->auth->verify2FA($request->user(), $request->validated());
        return response()->json(['ok' => true]);
    }
}
