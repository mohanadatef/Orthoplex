<?php

it('accept invitation requires token', function () {
    $this->postJson('/api/invitations/accept', [])->assertStatus(422);
});
