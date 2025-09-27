<?php

test('users listing returns json', function () {
    $this->getJson('/api/users')->assertStatus(200);
});
