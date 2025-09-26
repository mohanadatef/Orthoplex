<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TwoFactorSecret;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Hash;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TwoFactorService::class);
        $this->google2fa = new Google2FA();
    }

    /** @test */
    public function user_can_login_without_2fa()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /** @test */
    public function enabling_2fa_requires_valid_code()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        // Generate secret
        $secret = $this->service->generateSecret($user->id);

        // Fake valid TOTP
        $validCode = $this->google2fa->getCurrentOtp($secret);

        // Login to get JWT for enable endpoint
        $token = JWTAuth::fromUser($user);

        // Enable 2FA
        $res = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/2fa/enable', ['code' => $validCode]);

        $res->assertStatus(200)
            ->assertJsonStructure(['backup_codes']);
    }

    /** @test */
    public function login_with_2fa_returns_two_factor_required()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        // Setup 2FA
        $secret = $this->service->generateSecret($user->id);
        $this->service->enable2FA($user->id);

        // Try login → expect 2fa_required
        $res = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $res->assertStatus(200)
            ->assertJson(['2fa_required' => true, 'user_id' => $user->id]);
    }

    /** @test */
    public function verify_2fa_with_valid_code_returns_jwt()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        // Setup 2FA
        $secret = $this->service->generateSecret($user->id);
        $this->service->enable2FA($user->id);

        $validCode = $this->google2fa->getCurrentOtp($secret);

        // Verify & login
        $res = $this->postJson('/api/v1/2fa/verify-login', [
            'user_id' => $user->id,
            'code' => $validCode
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /** @test */
    public function verify_2fa_with_invalid_code_fails()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        // Setup 2FA
        $secret = $this->service->generateSecret($user->id);
        $this->service->enable2FA($user->id);

        // Wrong code
        $res = $this->postJson('/api/v1/2fa/verify-login', [
            'user_id' => $user->id,
            'code' => '123456'
        ]);

        $res->assertStatus(400)
            ->assertJson(['message' => 'Invalid 2FA code']);
    }

    /** @test */
    public function backup_code_can_be_used_once()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        $secret = $this->service->generateSecret($user->id);
        $this->service->enable2FA($user->id);

        $codes = $this->service->generateBackupCodes($user->id, 1);
        $backupCode = $codes[0];

        // Use backup code
        $res = $this->postJson('/api/v1/2fa/verify-login', [
            'user_id' => $user->id,
            'code' => $backupCode
        ]);

        $res->assertStatus(200)->assertJsonStructure(['token']);

        // Try again → should fail
        $res2 = $this->postJson('/api/v1/2fa/verify-login', [
            'user_id' => $user->id,
            'code' => $backupCode
        ]);

        $res2->assertStatus(400);
    }
}
