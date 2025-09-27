<?php

it('applies rsql filter safely', function () {
    $this->getJson('/api/users?filter=name==Alice')->assertStatus(200);
});
