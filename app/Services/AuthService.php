<?php

namespace App\Services;

use App\Repositories\Contracts\AuthRepositoryInterface;
use App\DTOs\UserDTO;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userService;
    public function __construct(
        private readonly AuthRepositoryInterface $repository,UserService $userService
    ) {
        $this->userService = $userService;
    }

    public function register(UserDTO $dto)
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }

    public function login(string $email, string $password)
    {
        $credentials = compact('email','password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return null;
        }

        $user = $this->repository->findByEmail($email);

        if (! $user->hasVerifiedEmail()) {
            return ['error' => 'email_not_verified'];
        }

        if ($user->twoFactorSecret && $user->twoFactorSecret->enabled) {
            return ['2fa_required' => true];
        }
        $ok = $this->userService->updateOptimistic($user, [
            'last_login_at' => now(),
            'login_count'   => $user->login_count + 1,
        ]);

        if (! $ok) {
            throw new \RuntimeException('Conflict: user record was modified concurrently.');
        }
        \App\Models\LoginEvent::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return $token;
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
