<?php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IdempotencyTest extends TestCase {
    use RefreshDatabase;

    public function test_idempotency_prevents_double_processing()
    {
        $key = 'test-key-123';
        $payload = ['email' => 'test@example.com','password'=>'123456'];

        $this->postJson('/api/register', $payload, ['Idempotency-Key' => $key])
            ->assertStatus(200);

        $this->postJson('/api/register', $payload, ['Idempotency-Key' => $key])
            ->assertStatus(200);
    }
}
