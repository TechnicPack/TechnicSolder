<?php

class UserTest extends TestCase {

	public function testUserIndex()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/user');

		$this->assertRedirectedTo('/user/list');
	}

	public function testUserCreateGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/user/create');

		$this->assertResponseOk();
	}

	public function testUserDeleteGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/user/delete/'.$user->id);

		$this->assertRedirectedTo('/dashboard');
	}

	public function testUserList()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/user/list');

		$this->assertResponseOk();
	}

	public function testUserEdit()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/user/edit/'.$user->id);

		$this->assertResponseOk();
	}
}
