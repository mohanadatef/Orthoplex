<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ApiKeyTest extends TestCase {
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();
        // create admin role and user
        Role::create(['name'=>'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_create_api_key_requires_admin() {
        $payload = ['name'=>'test-key'];
        $res = $this->actingAs($this->admin, 'api')->postJson('/api/v1/api-keys', $payload);
        $res->assertStatus(200)->assertJsonStructure(['api_key','id']);
    }
}
