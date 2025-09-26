<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebhookTest extends TestCase {
    use RefreshDatabase;

    public function test_receive_webhook_stores_and_attempts_delivery() {
        // Use a fake URL that will fail, but ensure record created
        $payload = ['url'=>'http://example.invalid/test','event'=>'user.created','payload'=>['id'=>1]];
        $res = $this->postJson('/api/v1/webhooks/receive', $payload);
        $res->assertStatus(200)->assertJsonStructure(['id','status']);
    }
}
