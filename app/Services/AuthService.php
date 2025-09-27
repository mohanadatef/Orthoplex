<?php
namespace App\Services;

use App\Models\{User, Org, EmailVerificationToken, MagicLink};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Auth\AuthenticationException;
use App\Mail\VerifyEmailMail;
use App\Mail\MagicLinkMail;

final class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $org = Org::create([
                'name' => $data['org_name'],
                'slug' => Str::slug($data['org_name']) . '-' . Str::random(4)
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'org_id' => $org->id
            ]);

            // Send email verification
            $raw = Str::random(48);
            $token = hash('sha256', $raw);
            EmailVerificationToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => now()->addDay(),
            ]);
            $url = url('/api/auth/verify-email?token='.$raw);
            Mail::to($user->email)->queue(new VerifyEmailMail($url));

            return ['user_id' => $user->id, 'org_id' => $org->id];
        });
    }

    public function login(array $data): array
    {
        $credentials = ['email' => $data['email'], 'password' => $data['password']];
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }
        $user = auth()->user();
        if (!$user->email_verified_at) {
            throw new AuthenticationException('Email not verified');
        }
        $user->last_login_at = now();
        $user->login_count = (int)$user->login_count + 1;
        $user->save();

        DB::table('login_events')->insert([
            'user_id' => $user->id,
            'org_id' => $user->org_id,
            'occurred_at' => now(),
            'ip' => request()->ip(),
            'ua' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl'),
            'user' => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email]
        ];
    }

    public function logout(User $user): void
    {
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::invalidate($token);
        }
    }

    public function verifyEmail(array $data): void
    {
        $raw = $data['token'];
        $token = hash('sha256', $raw);
        $row = EmailVerificationToken::where('token',$token)->firstOrFail();
        if ($row->used_at || ($row->expires_at && now()->greaterThan($row->expires_at))) {
            abort(410, 'Token expired');
        }
        $user = User::findOrFail($row->user_id);
        $user->email_verified_at = now();
        $user->save();
        $row->used_at = now();
        $row->save();
    }

    public function sendMagicLink(array $data): void
    {
        $raw = Str::random(48);
        $token = hash('sha256', $raw);
        MagicLink::create([
            'email' => $data['email'],
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
        ]);
        $url = url('/api/auth/magic-link/consume?token='.$raw);
        Mail::to($data['email'])->queue(new MagicLinkMail($url));
    }

    public function consumeMagicLink(string $raw): array
    {
        $token = hash('sha256', $raw);
        $row = MagicLink::where('token',$token)->firstOrFail();
        if ($row->used_at || ($row->expires_at && now()->greaterThan($row->expires_at))) {
            abort(410, 'Link expired');
        }
        $user = User::where('email',$row->email)->firstOrFail();
        $row->used_at = now();
        $row->save();
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }
        $jwt = JWTAuth::fromUser($user);
        return [
            'access_token' => $jwt,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl'),
            'user' => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email]
        ];
    }

    public function enable2FA(User $user): array
    {
        $secret = bin2hex(random_bytes(10));
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = true;
        $user->save();

        return ['otpauth_url' => 'otpauth://totp/Orthoplex:' . $user->email . '?secret=' . $secret];
    }

    public function verify2FA(User $user, array $data): void
    {
        // integrate with chosen TOTP lib (google2fa) as needed
    }
}
