<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class GDPRTest extends TestCase {
    use RefreshDatabase;

    public function test_export_dispatches_job() {
        $user = User::factory()->create();
        $res = $this->actingAs($user, 'api')->postJson('/api/v1/gdpr/export');
        $res->assertStatus(200)->assertJson(['message'=>'Export started']);
    }

    public function test_delete_request_created() {
        $user = User::factory()->create();
        $res = $this->actingAs($user, 'api')->postJson('/api/v1/gdpr/delete-request', ['reason'=>'No longer needed']);
        $res->assertStatus(200)->assertJsonStructure(['message','id']);
    }
}
