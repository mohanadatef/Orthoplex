<?php
namespace App\Services;
use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService {
    protected $users;
    public function __construct(UserRepository $users) {
        $this->users = $users;
    }

    public function register(UserDTO $dto) {
        $user = $this->users->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'locale' => $dto->locale,
        ]);
        $user->notify(new \App\Notifications\VerifyEmailNotification($user));
        return $user;
    }

    public function login(string $email, string $password) {
        $user = $this->users->findByEmail($email);
        if (!$user) return null;

        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return null;
        }

        if (! $user->hasVerifiedEmail()) {
            return ['error' => 'email_not_verified'];
        }

        if ($user->twoFactorSecret && $user->twoFactorSecret->enabled) {
            return ['2fa_required' => true, 'user_id' => $user->id];
        }

        $credentials = compact('email','password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }


        return $token;
    }

    public function logout() {
        JWTAuth::parseToken()->invalidate();
    }
}
