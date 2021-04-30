<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class BaseTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Session::start();
    }

    public function testLoginGet()
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function testUnauthorizedLogin()
    {
        $credentials = [
            'email' => 'test@admin.com',
            'password' => 'ifail',
            'remember' => false
        ];

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/login');
        $response->assertSessionHas('login_failed');
    }

    public function testAuthorizedLogin()
    {
        $credentials = [
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'remember' => false
        ];

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/dashboard');
    }

    public function testIndex()
    {
        $user = User::find(1);
        $this->be($user);

        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function testUnauthorizedAccess()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function testDashboard()
    {
        $user = User::find(1);
        $this->be($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
    }
}
