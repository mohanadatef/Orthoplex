<?php

it('fails login with bad creds', function () {
    $this->postJson('/api/auth/login', ['email'=>'x@y.com','password'=>'bad'])->assertStatus(401);
});
