<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiKeyService
{
    public function generate(int $userId, int $validDays = 30): ApiKey
    {
        return ApiKey::create([
            'user_id' => $userId,
            'key'     => Str::random(32),
            'secret'  => Str::random(64),
            'expires_at' => Carbon::now()->addDays($validDays),
        ]);
    }

    public function rotate(ApiKey $apiKey, int $graceDays = 7): ApiKey
    {
        $oldKey = $apiKey->replicate();
        $oldKey->grace_until = Carbon::now()->addDays($graceDays);
        $oldKey->save();

        $apiKey->key = Str::random(32);
        $apiKey->secret = Str::random(64);
        $apiKey->expires_at = Carbon::now()->addDays(30);
        $apiKey->grace_until = null;
        $apiKey->save();

        return $apiKey;
    }

    public function verifyHmac(ApiKey $apiKey, string $signature, string $payload): bool
    {
        $expected = hash_hmac('sha256', $payload, $apiKey->secret);

        return hash_equals($expected, $signature);
    }
}
