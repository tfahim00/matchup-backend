<?php

namespace Tests\Feature;

use Tests\TestCase;

class PasswordResetRouteTest extends TestCase
{
    public function test_password_reset_page_is_available_from_the_reset_link(): void
    {
        $response = $this->get('/reset-password/test-token?email=test@example.com');

        $response->assertStatus(200);
        $response->assertSee('Reset Your Password');
    }

    public function test_debug_routes_are_not_exposed(): void
    {
        $this->get('/test')->assertNotFound();
        $this->get('/login-test')->assertNotFound();
        $this->get('/api/log-test')->assertNotFound();
    }
}
