<?php
namespace App\Services;

use App\Models\TwoFactorSecret;
use App\Models\TwoFactorBackupCode;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class TwoFactorService
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Generate and store encrypted secret (not enabled until user confirms)
     */
    public function generateSecret(int $userId): string
    {
        $secret = $this->google2fa->generateSecretKey();
        // store encrypted
        TwoFactorSecret::updateOrCreate(
            ['user_id' => $userId],
            ['secret' => Crypt::encryptString($secret), 'enabled' => false]
        );
        return $secret;
    }

    /**
     * Enable 2FA after verifying the code
     */
    public function enable2FA(int $userId): ?TwoFactorSecret
    {
        $secret = TwoFactorSecret::where('user_id', $userId)->first();
        if (!$secret) return null;
        $secret->enabled = true;
        $secret->save();
        return $secret;
    }

    /**
     * Generate N backup codes: return raw codes to user, store hashed in DB.
     */
    public function generateBackupCodes(int $userId, int $count = 8): array
    {
        // remove old codes
        TwoFactorBackupCode::where('user_id', $userId)->delete();

        $raw = [];
        for ($i = 0; $i < $count; $i++) {
            // human-friendly code: 8 chars uppercase alnum
            $code = Str::upper(Str::random(8));
            TwoFactorBackupCode::create([
                'user_id' => $userId,
                'code' => Hash::make($code),
                'used' => false
            ]);
            $raw[] = $code;
        }
        return $raw;
    }

    /**
     * Verify provided code (TOTP) or backup code.
     * If backup code matches, mark it used.
     */
    public function verifyCode(int $userId, string $code): bool
    {
        $secretModel = TwoFactorSecret::where('user_id', $userId)->where('enabled', true)->first();
        if ($secretModel) {
            $secret = Crypt::decryptString($secretModel->secret);
            // allow small window (default verifyKey uses window 0). You can use verifyKeyNew if needed.
            if ($this->google2fa->verifyKey($secret, $code)) {
                return true;
            }
        }

        // check backup codes (hashed)
        $backup = TwoFactorBackupCode::where('user_id', $userId)->where('used', false)->get();
        foreach ($backup as $b) {
            if (Hash::check($code, $b->code)) {
                // mark used (single-use)
                $b->used = true;
                $b->save();
                return true;
            }
        }

        return false;
    }
}
