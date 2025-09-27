<?php

it('returns same response with same idempotency key', function () {
    $payload = ['name'=>'A','email'=>'a@example.com','password'=>'password123'];
    $headers = ['Idempotency-Key' => 'abc123'];
    $this->postJson('/api/auth/register', $payload, $headers)->assertStatus(201);
    $this->postJson('/api/auth/register', $payload, $headers)->assertStatus(201);
});
