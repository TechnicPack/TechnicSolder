<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

final class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function confirmPassword(): void
    {
        session()->put('auth.password_confirmed_at', time());
    }

    private function enableAndConfirm2FA(User $user): void
    {
        $this->actingAs($user);
        $this->confirmPassword();

        $this->post('/user/two-factor-authentication');

        $user->refresh();

        $google2fa = new Google2FA;
        $secret = decrypt($user->two_factor_secret);
        $code = $google2fa->getCurrentOtp($secret);

        $this->post('/user/confirmed-two-factor-authentication', ['code' => $code]);

        $user->refresh();
    }

    private function createUserWithPermissions(array $perms = []): User
    {
        $user = new User;
        $user->username = 'testuser2fa';
        $user->email = 'test2fa@example.com';
        $user->password = 'password';
        $user->created_ip = '127.0.0.1';
        $user->created_by_user_id = 1;
        $user->updated_by_user_id = 1;
        $user->updated_by_ip = '127.0.0.1';
        $user->save();

        $permission = new UserPermission;
        $permission->user_id = $user->id;
        foreach ($perms as $key => $value) {
            $permission->{$key} = $value;
        }
        $permission->save();

        $user->load('permission');

        return $user;
    }

    // --- Setup flow ---

    public function test_user_can_enable_2fa(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $this->confirmPassword();

        $this->post('/user/two-factor-authentication');

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
    }

    public function test_user_can_see_qr_code_after_enabling(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $this->confirmPassword();

        $this->post('/user/two-factor-authentication');

        $user->refresh();

        $response = $this->get('/user/edit/'.$user->id);
        $response->assertOk();
        $response->assertSee('<svg', false);
    }

    public function test_user_can_confirm_2fa_with_valid_code(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $this->confirmPassword();

        $this->post('/user/two-factor-authentication');

        $user->refresh();

        $google2fa = new Google2FA;
        $secret = decrypt($user->two_factor_secret);
        $code = $google2fa->getCurrentOtp($secret);

        $this->post('/user/confirmed-two-factor-authentication', ['code' => $code]);

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_user_cannot_confirm_2fa_with_invalid_code(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $this->confirmPassword();

        $this->post('/user/two-factor-authentication');

        $response = $this->post('/user/confirmed-two-factor-authentication', ['code' => '000000']);

        $response->assertSessionHasErrors();

        $user->refresh();
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_user_can_view_recovery_codes_after_confirmation(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $this->assertNotNull($user->two_factor_recovery_codes);

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        $this->assertIsArray($codes);
        $this->assertCount(8, $codes);
    }

    public function test_user_can_regenerate_recovery_codes(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $oldCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $this->post('/user/two-factor-recovery-codes');

        $user->refresh();
        $newCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $this->assertNotEquals($oldCodes, $newCodes);
    }

    public function test_user_can_disable_2fa(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $this->delete('/user/two-factor-authentication');

        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    // --- Login challenge flow ---

    public function test_user_with_2fa_gets_redirected_to_challenge(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);
        auth()->logout();

        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/two-factor-challenge');
        $this->assertGuest();
    }

    private function loginWithChallenge(User $user): void
    {
        // Simulate Fortify's 2FA challenge session state
        session()->put('login.id', $user->id);
        session()->put('login.remember', false);
    }

    public function test_two_factor_challenge_view_renders(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);
        auth()->logout();

        $this->loginWithChallenge($user);

        $response = $this->get('/two-factor-challenge');
        $response->assertOk();
        $response->assertSee('Two-Factor Authentication');
    }

    public function test_valid_totp_code_completes_login(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $secret = decrypt($user->two_factor_secret);
        auth()->logout();

        // Clear 2FA replay cache so the same OTP window isn't rejected
        Cache::flush();

        $this->loginWithChallenge($user);

        $google2fa = new Google2FA;
        $code = $google2fa->getCurrentOtp($secret);

        $response = $this->post('/two-factor-challenge', ['code' => $code]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_invalid_totp_code_rejected(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);
        auth()->logout();

        $this->loginWithChallenge($user);

        $response = $this->post('/two-factor-challenge', ['code' => '000000']);
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_valid_recovery_code_completes_login(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        auth()->logout();

        $this->loginWithChallenge($user);

        $response = $this->post('/two-factor-challenge', ['recovery_code' => $codes[0]]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_used_recovery_code_cannot_be_reused(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        $usedCode = $codes[0];
        auth()->logout();

        // Use the code first time
        $this->loginWithChallenge($user);
        $this->post('/two-factor-challenge', ['recovery_code' => $usedCode]);
        auth()->logout();

        // Try to use it again
        $this->loginWithChallenge($user);
        $response = $this->post('/two-factor-challenge', ['recovery_code' => $usedCode]);
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_invalid_recovery_code_rejected(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);
        auth()->logout();

        $this->loginWithChallenge($user);

        $response = $this->post('/two-factor-challenge', ['recovery_code' => 'invalid-code']);
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_login_updates_last_ip(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::find(1);
        $this->assertEquals('127.0.0.1', $user->last_ip);
    }

    public function test_user_without_2fa_logs_in_normally(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    // --- Password confirmation endpoint (used by modal) ---

    public function test_confirm_password_returns_201_with_valid_password(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->postJson('/user/confirm-password', ['password' => 'admin']);
        $response->assertStatus(201);
    }

    public function test_confirm_password_returns_422_with_invalid_password(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->postJson('/user/confirm-password', ['password' => 'wrong']);
        $response->assertStatus(422);
    }

    public function test_enable_2fa_requires_password_confirmation(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        // Without confirming password first, should be redirected to confirm
        $response = $this->post('/user/two-factor-authentication');
        $response->assertRedirect('/user/confirm-password');
    }

    public function test_disable_2fa_requires_password_confirmation(): void
    {
        $user = User::find(1);
        $this->enableAndConfirm2FA($user);

        // Clear confirmation
        session()->forget('auth.password_confirmed_at');

        $response = $this->delete('/user/two-factor-authentication');
        $response->assertRedirect('/user/confirm-password');

        // 2FA should still be enabled
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
    }

    // --- Admin management ---

    public function test_admin_can_reset_user_2fa(): void
    {
        $targetUser = $this->createUserWithPermissions();
        $this->actingAs($targetUser);
        $this->enableAndConfirm2FA($targetUser);

        $admin = User::find(1);

        $response = $this->actingAs($admin)
            ->post('/user/'.$targetUser->id.'/reset-2fa');

        $response->assertRedirect('user/edit/'.$targetUser->id);
        $targetUser->refresh();
        $this->assertNull($targetUser->two_factor_secret);
        $this->assertNull($targetUser->two_factor_confirmed_at);
    }

    public function test_admin_with_solder_users_can_reset_2fa(): void
    {
        $targetUser = $this->createUserWithPermissions();
        $this->actingAs($targetUser);
        $this->enableAndConfirm2FA($targetUser);

        $admin = $this->createUserWithPermissions(['solder_users' => true]);
        $admin->username = 'adminuser';
        $admin->email = 'admin2@example.com';
        $admin->save();

        $response = $this->actingAs($admin)
            ->post('/user/'.$targetUser->id.'/reset-2fa');

        $response->assertRedirect('user/edit/'.$targetUser->id);
        $targetUser->refresh();
        $this->assertNull($targetUser->two_factor_secret);
    }

    public function test_non_admin_cannot_reset_user_2fa(): void
    {
        $targetUser = $this->createUserWithPermissions();
        $this->actingAs($targetUser);
        $this->enableAndConfirm2FA($targetUser);

        $regularUser = $this->createUserWithPermissions();
        $regularUser->username = 'regular';
        $regularUser->email = 'regular@example.com';
        $regularUser->save();

        $perm = new UserPermission;
        $perm->user_id = $regularUser->id;
        $perm->save();
        $regularUser->load('permission');

        $response = $this->actingAs($regularUser)
            ->post('/user/'.$targetUser->id.'/reset-2fa');

        $response->assertRedirect('dashboard');
    }

    public function test_user_list_shows_2fa_badges(): void
    {
        $admin = User::find(1);

        $response = $this->actingAs($admin)->get('/user/list');
        $response->assertOk();
        $response->assertSee('two_factor');
    }
}
