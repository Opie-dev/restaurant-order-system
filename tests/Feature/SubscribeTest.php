<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_subscribe_page(): void
    {
        $response = $this->get('/subscribe');
        $response->assertOk();
        $response->assertSee('Early Access Subscription');
    }
}
