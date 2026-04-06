<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function enableMail(): void
    {
        Config::set('solder.mail_enabled', true);
    }

    // --- Mail disabled (default) ---

    public function test_forgot_password_route_404_when_mail_disabled(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(404);
    }

    public function test_reset_password_post_404_when_mail_disabled(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'admin@example.com']);

        $response->assertStatus(404);
    }

    public function test_login_page_has_no_forgot_link_when_mail_disabled(): void
    {
        $response = $this->get('/login');

        $response->assertDontSee('Forgot password?');
    }

    // --- Mail enabled ---

    public function test_forgot_password_view_renders_when_mail_enabled(): void
    {
        $this->enableMail();

        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertSee('Send Reset Link');
    }

    public function test_login_page_has_forgot_link_when_mail_enabled(): void
    {
        $this->enableMail();

        $response = $this->get('/login');

        $response->assertSee('Forgot password?');
    }

    public function test_reset_link_sent_for_valid_email(): void
    {
        $this->enableMail();
        Notification::fake();

        $user = User::first();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_link_not_sent_for_invalid_email(): void
    {
        $this->enableMail();
        Notification::fake();

        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        $response->assertRedirect();
        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $this->enableMail();
        Notification::fake();

        $user = User::first();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

            $response->assertRedirect('/login');

            return true;
        });

        // Verify new password works
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password-123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $this->enableMail();

        $user = User::first();

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_reset_password_view_renders(): void
    {
        $this->enableMail();

        $response = $this->get('/reset-password/test-token');

        $response->assertOk();
        $response->assertSee('Set New Password');
    }
}
