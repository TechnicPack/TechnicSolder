<?php

class BaseTest extends TestCase {

	public function testLoginGet()
	{
		Route::enableFilters();
		$this->call('GET', '/login');

		$this->assertResponseOk();
	}

	public function testIndex()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/');

		$this->assertRedirectedTo('/dashboard');
	}

	public function testUnauthorizedAccess()
	{
		Route::enableFilters();
		$this->call('GET', '/dashboard');

		$this->assertRedirectedTo('/login');
	}

	public function testDashboard()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/dashboard');

		$this->assertResponseOk();
	}
}