<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'wrong',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'anything',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_empty_fields(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_updates_last_ip(): void
    {
        $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $user = User::find(1);
        $this->assertNotNull($user->last_ip);
    }

    public function test_login_with_remember_sets_token(): void
    {
        $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'remember' => true,
        ]);

        $user = User::find(1);
        $this->assertNotNull($user->remember_token);
    }

    public function test_authenticated_user_visiting_login_redirects_to_dashboard(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/login');
        $response->assertRedirect('/dashboard');
    }

    public function test_logout_invalidates_session_and_redirects(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_logout_flashes_message(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertSessionHas('status', 'You have been logged out.');
    }

    public function test_rate_limiting_blocks_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'admin@admin.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_successful_login_after_failed_attempts(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'email' => 'admin@admin.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
