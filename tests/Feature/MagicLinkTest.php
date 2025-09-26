<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MagicLinkService;

class MagicLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_request_and_login_with_magic_link()
    {
        $user = User::factory()->create();
        $service = app(MagicLinkService::class);

        $link = $service->createLink($user);

        $res = $this->getJson('/api/v1/magic-link/verify?token='.$link->token);
        $res->assertStatus(200)->assertJsonStructure(['token']);
    }
}
