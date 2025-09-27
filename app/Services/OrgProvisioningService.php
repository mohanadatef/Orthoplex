<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\Provisioning\ProvisioningSaga;
use App\Models\{Org, User};

final class OrgProvisioningService
{
    public function provisionInbound(Request $request): void
    {
        $apiKey = $request->header('X-Api-Key');
        if ($apiKey !== env('INBOUND_API_KEY')) abort(401, 'Invalid API key');

        $body = $request->getContent();
        $sig = $request->header('X-Signature');
        $calc = hash_hmac('sha256', $body, env('WEBHOOK_HMAC_SECRET','dev-secret'));
        if (!hash_equals($calc, (string)$sig)) abort(401, 'Invalid signature');

        $data = $request->validate([
            'org_name' => ['required','string','max:120'],
            'owner_name' => ['required','string','max:100'],
            'owner_email' => ['required','email'],
            'owner_password' => ['required','string','min:8'],
        ]);

        ((new ProvisioningSaga())->run(function(ProvisioningSaga $saga) use ($data) {
            $org = Org::create([
                'name' => $data['org_name'],
                'slug' => Str::slug($data['org_name']).'-'.Str::random(4),
                'webhook_secret' => bin2hex(random_bytes(16)),
            ]);

            $user = User::create([
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => Hash::make($data['owner_password']),
                'org_id' => $org->id,
                'email_verified_at' => now(),
            ]);

            // attach owner role if using spatie
            if (method_exists($user, 'assignRole')) $user->assignRole('owner');
        }));
    }
}
