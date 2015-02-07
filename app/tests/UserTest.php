<?php

class UserTest extends TestCase {

	public function testUserIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/user');

		$this->assertRedirectedTo('/user/list');
	}

	public function testUserCreateGet()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/user/create');

		$this->client->getResponse()->isOk();
	}

	public function testUserDeleteGet()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/user/delete/'.$user->id);

		$this->client->getResponse()->isOk();
	}

	public function testUserList()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/user/list');

		$this->client->getResponse()->isOk();
	}

	public function testUserEdit()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/user/edit/'.$user->id);

		$this->client->getResponse()->isOk();
	}
}
