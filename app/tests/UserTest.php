<?php

class UserTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testUserIndexGet()
	{
		$this->call('GET', '/user');

		$this->assertRedirectedTo('/user/list');
	}

	public function testUserCreateGet()
	{
		$this->call('GET', '/user/create');

		$this->assertResponseOk();
	}

	public function testUserDeleteGet()
	{
		$user = User::find(1);

		$this->call('GET', '/user/delete/'.$user->id);

		$this->assertRedirectedTo('/dashboard');
	}

	public function testUserListGet()
	{
		$this->call('GET', '/user/list');

		$this->assertResponseOk();
	}

	public function testUserEditGet()
	{
		$user = User::find(1);
		
		$this->call('GET', '/user/edit/'.$user->id);

		$this->assertResponseOk();
	}
}
