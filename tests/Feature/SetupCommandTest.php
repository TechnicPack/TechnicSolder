<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SetupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_creates_admin_user_non_interactively(): void
    {
        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->expectsOutput("Admin user 'admin' created successfully.")
            ->assertExitCode(0);

        $user = User::where('username', 'admin')->first();

        $this->assertNotNull($user);
        $this->assertEquals('admin@admin.com', $user->email);
        $this->assertEquals(1, $user->permission->solder_full);
    }

    public function test_setup_generates_random_password_when_not_provided(): void
    {
        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->expectsOutputToContain('Generated password:')
            ->expectsOutput('Change this password immediately after first login.')
            ->assertExitCode(0);
    }

    public function test_setup_uses_env_password_when_provided(): void
    {
        putenv('SOLDER_INITIAL_ADMIN_PASSWORD=secretpass123');

        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->doesntExpectOutputToContain('Generated password:')
            ->assertExitCode(0);

        putenv('SOLDER_INITIAL_ADMIN_PASSWORD');
    }

    public function test_setup_uses_env_email_when_provided(): void
    {
        putenv('SOLDER_INITIAL_ADMIN_EMAIL=custom@example.com');

        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->assertExitCode(0);

        $this->assertEquals('custom@example.com', User::first()->email);

        putenv('SOLDER_INITIAL_ADMIN_EMAIL');
    }

    public function test_setup_skips_when_users_exist(): void
    {
        $this->seed();

        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->expectsOutput('An admin user already exists. Skipping setup.')
            ->assertExitCode(0);
    }

    public function test_setup_accepts_custom_username(): void
    {
        $this->artisan('solder:setup', ['--username' => 'superadmin', '--no-interaction' => true])
            ->expectsOutput("Admin user 'superadmin' created successfully.")
            ->assertExitCode(0);

        $this->assertEquals('superadmin', User::first()->username);
    }

    public function test_setup_creates_full_permissions(): void
    {
        $this->artisan('solder:setup', ['--no-interaction' => true])
            ->assertExitCode(0);

        $permission = UserPermission::first();

        $this->assertNotNull($permission);
        $this->assertEquals(1, $permission->solder_full);
    }
}
