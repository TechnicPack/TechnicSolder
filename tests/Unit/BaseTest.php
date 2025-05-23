<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

final class BaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Session::start();
    }

    public function test_login_get(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_unauthorized_login(): void
    {
        $credentials = [
            'email' => 'test@admin.com',
            'password' => 'ifail',
            'remember' => false,
        ];

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/login');
        $response->assertSessionHas('login_failed');
    }

    public function test_authorized_login(): void
    {
        $credentials = [
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'remember' => false,
        ];

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/dashboard');
    }

    public function test_index(): void
    {
        $user = User::find(1);
        $this->be($user);

        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function test_unauthorized_access(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard(): void
    {
        $user = User::find(1);
        $this->be($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
    }
}
