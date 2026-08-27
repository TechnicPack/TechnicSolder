<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
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
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
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
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $user = User::find(1);
        $this->assertNotNull($user->last_ip);
    }

    private function enableAndConfirm2FA(User $user): void
    {
        $this->actingAs($user);
        session()->put('auth.password_confirmed_at', time());

        $this->post('/user/two-factor-authentication');

        $user->refresh();

        $google2fa = new Google2FA;
        $this->post('/user/confirmed-two-factor-authentication', [
            'code' => $google2fa->getCurrentOtp(decrypt($user->two_factor_secret)),
        ]);

        $user->refresh();
        auth()->logout();
    }

    public function test_login_side_effects_do_not_run_before_two_factor_challenge(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $user->last_ip = '10.0.0.1';
        $user->save();

        // Correct password, but the second factor has not been supplied yet.
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/two-factor-challenge');
        $this->assertGuest();

        $user->refresh();
        $this->assertSame('10.0.0.1', $user->last_ip, 'last_ip must not be recorded until the 2FA challenge passes.');
    }

    public function test_last_ip_is_updated_after_two_factor_challenge_completes(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $user->last_ip = '10.0.0.1';
        $user->save();

        $secret = decrypt($user->two_factor_secret);

        // Confirming 2FA above consumed the current OTP window; clear the
        // replay cache so the same code is accepted for the challenge.
        Cache::flush();

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $google2fa = new Google2FA;
        $response = $this->post('/two-factor-challenge', [
            'code' => $google2fa->getCurrentOtp($secret),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user->refresh();
        $this->assertSame('127.0.0.1', $user->last_ip);
    }

    public function test_password_is_rehashed_on_login(): void
    {
        $user = User::find(1);

        // Plant the same password at a work factor that differs from the
        // configured one (phpunit.xml pins BCRYPT_ROUNDS to 4) so the guard has
        // a reason to rehash it. Written straight to the column because the
        // `hashed` cast refuses to store a hash the current config wouldn't
        // produce, which is precisely the situation being simulated.
        $staleHash = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 6]);
        DB::table('users')->where('id', $user->id)->update(['password' => $staleHash]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $this->assertAuthenticated();

        $user->refresh();
        $this->assertNotSame($staleHash, $user->password, 'The outdated hash must be upgraded on login.');
        $this->assertTrue(Hash::check('admin', $user->password));
    }

    public function test_login_with_remember_sets_token(): void
    {
        $this->post('/login', [
            'email' => 'admin@example.com',
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
                'email' => 'admin@example.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_rate_limiting_is_not_bypassed_by_email_casing(): void
    {
        // Fortify lowercases the username when authenticating, so every casing
        // variant below targets the same account and must share one bucket.
        $variants = [
            'admin@example.com',
            'Admin@example.com',
            'ADMIN@EXAMPLE.COM',
            'AdMiN@ExAmPlE.cOm',
            'aDMIN@example.COM',
        ];

        foreach ($variants as $variant) {
            $this->post('/login', ['email' => $variant, 'password' => 'wrong']);
        }

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_rate_limiting_is_not_bypassed_by_transliteration(): void
    {
        // Accented homoglyphs fold to plain ASCII in the throttle key, so they
        // cannot be used to mint a fresh bucket for the same target address.
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['email' => 'admin@example.com', 'password' => 'wrong']);
        }

        foreach (['ádmin@example.com', 'admın@example.com'] as $homoglyph) {
            $this->post('/login', ['email' => $homoglyph, 'password' => 'wrong']);
        }

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_successful_login_after_failed_attempts(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
