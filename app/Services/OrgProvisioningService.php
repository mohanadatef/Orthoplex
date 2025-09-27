<?php

namespace App\Services;

use App\Models\Org;
use App\Models\User;
use App\Repositories\Contracts\ApiKeyRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrgProvisioningService
{
    public function __construct(private ApiKeyRepositoryInterface $apiKeys) {}

    public function provision(string $orgName, array $users, string $signature, string $apiKey)
    {
        $key = $this->apiKeys->findByKey($apiKey);
        if (! $key) return null;

        // HMAC verification
        $payload = json_encode(['org_name' => $orgName, 'users' => $users]);
        $expected = hash_hmac('sha256', $payload, $key->secret);
        if (! hash_equals($expected, $signature)) {
            return null;
        }

        return DB::transaction(function () use ($orgName, $users) {
            $org = Org::create(['name' => $orgName]);

            foreach ($users as $u) {
                $user = User::firstOrCreate(['email' => $u['email']], [
                    'name' => $u['email'],
                    'password' => Hash::make(str()->random(16)),
                ]);
                $org->users()->attach($user->id, ['role' => $u['role']]);
            }

            return $org;
        });
    }
}
