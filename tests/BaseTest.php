<?php

use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class BaseTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Session::start();
	}

	public function testLoginGet()
	{
		$this->call('GET', '/auth/login');

		$this->assertResponseOk();
	}

	public function testUnauthorizedLogin()
	{
		$credentials = array(
			'email' => 'test@admin.com',
			'password' => 'ifail',
			'remember' => false
		);

		$response = $this->call('POST', '/auth/login', $credentials);
		$this->assertRedirectedTo('/auth/login');
		$this->assertSessionHas('login_failed');
	}

	public function testAuthorizedLogin()
	{
		$credentials = array(
			'email' => 'admin@admin.com',
			'password' => 'admin',
			'remember' => false
		);

		$response = $this->call('POST', '/auth/login', $credentials);
		$this->assertRedirectedTo('/dashboard');
	}

	public function testIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/');

		$this->assertRedirectedTo('/dashboard');
	}

	public function testUnauthorizedAccess()
	{
		$this->call('GET', '/dashboard');

		$this->assertRedirectedTo('/auth/login');
	}

	public function testDashboard()
	{
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/dashboard');

		$this->assertResponseOk();
	}
}
