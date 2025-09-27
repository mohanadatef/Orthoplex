<?php

it('protects user management routes', function () {
    $this->postJson('/api/users', [])->assertStatus(401);
});
