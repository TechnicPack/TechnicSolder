<?php

class ClientTest extends TestCase {

	public function testClientIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client');

		$this->assertRedirectedTo('/client/list');
	}

	public function testClientCreateGet()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client/create');

		$this->assertResponseOk();
	}

	public function testClientDeleteGet()
	{
		$user = User::find(1);
		$client = Client::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client/delete/'.$client->id);

		$this->assertResponseOk();
	}

	public function testClientList()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client/list');

		$this->assertResponseOk();
	}
}
