<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RateLimitTest extends TestCase {
    use RefreshDatabase;
    public function test_login_rate_limit_blocks_after_too_many_attempts() {
        for($i=0;$i<5;$i++) {
            $this->postJson('/api/v1/auth/login',['email'=>'fake@example.com','password'=>'wrong']);
        }
        $res = $this->postJson('/api/v1/auth/login',['email'=>'fake@example.com','password'=>'wrong']);
        $res->assertStatus(429);
    }
}
