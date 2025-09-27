<?php
namespace App\Services;

use App\Models\{OrgApiKey, User};
use Illuminate\Support\Str;

final class OrgApiKeyService
{
    public function create(User $actor, string $name, array $scopes = []): array
    {
        $raw = Str::random(56);
        $key = hash('sha256', $raw);
        $rec = OrgApiKey::create([
            'org_id' => $actor->org_id,
            'name' => $name,
            'key' => $key,
            'scopes' => $scopes,
        ]);
        return ['id'=>$rec->id, 'token'=>$raw];
    }

    public function revoke(User $actor, int $id): void
    {
        $k = OrgApiKey::where('org_id',$actor->org_id)->findOrFail($id);
        $k->revoked_at = now();
        $k->save();
    }

    public static function resolveRaw(string $raw): ?OrgApiKey
    {
        $key = hash('sha256', $raw);
        return OrgApiKey::where('key',$key)->whereNull('revoked_at')->first();
    }
}
