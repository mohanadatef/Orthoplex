<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ApiKey;

class ApiKeyGracePeriodTest extends TestCase {
    use RefreshDatabase;
    public function test_rotated_api_key_still_works_during_grace_period() {
        $apiKey = ApiKey::factory()->create(['grace_period_ends_at'=>now()->addMinutes(10)]);
        $this->withHeaders(['X-API-KEY'=>$apiKey->key])->getJson('/api/v1/protected')->assertStatus(200);
        $apiKey->rotateKey(); // simulate rotation
        $this->withHeaders(['X-API-KEY'=>$apiKey->key])->getJson('/api/v1/protected')->assertStatus(200);
    }
}
