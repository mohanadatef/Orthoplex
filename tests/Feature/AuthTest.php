<?php

test('register endpoint exists', function () {
    $this->postJson('/api/auth/register', [
        'name'=>'T','email'=>'t@example.com','password'=>'password123','password_confirmation'=>'password123','org_name'=>'Acme'
    ])->assertStatus(201);
});
