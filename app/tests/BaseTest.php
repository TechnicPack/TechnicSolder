<?php

class BaseTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Session::start();

		Route::enableFilters();
	}

	public function testLoginGet()
	{
		$this->call('GET', '/login');

		$this->assertResponseOk();
	}

	public function testUnauthorizedLogin()
	{
		$credentials = array(
			'email' => 'test@admin.com',
			'password' => 'ifail',
			'remember' => false
		);

		$response = $this->call('POST', '/login', $credentials);
		$this->assertRedirectedTo('/login');
		$this->assertSessionHas('login_failed');
	}

	public function testAuthorizedLogin()
	{
		$credentials = array(
			'email' => 'admin@admin.com',
			'password' => 'admin',
			'remember' => false
		);

		$response = $this->call('POST', '/login', $credentials);
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

		$this->assertRedirectedTo('/login');
	}

	public function testDashboard()
	{
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/dashboard');

		$this->assertResponseOk();
	}
}
