<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;

class ApiKeyController extends Controller {
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'scopes' => 'array',
            'expires_in_days' => 'nullable|integer',
        ]);
        $key = ApiKey::create([
            'name' => $data['name'],
            'key' => ApiKey::generateKey(),
            'scopes' => $data['scopes'] ?? [],
            'expires_at' => isset($data['expires_in_days']) ? Carbon::now()->addDays($data['expires_in_days']) : null
        ]);
        return response()->json(['api_key' => $key->key,'id'=>$key->id]);
    }

    public function rotate($id) {
        $apiKey = ApiKey::findOrFail($id);
        $old = $apiKey->key;
        $apiKey->rotated_at = now();
        $apiKey->key = ApiKey::generateKey();
        $apiKey->save();
        return response()->json(['old_key_masked' => substr($old,0,8) . '...','new_key'=>$apiKey->key]);
    }
}
