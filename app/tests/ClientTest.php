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

		$this->client->getResponse()->isOk();
	}

	public function testClientDeleteGet()
	{
		$user = User::find(1);
		$client = Client::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client/delete/'.$client->id);

		$this->client->getResponse()->isOk();
	}

	public function testClientList()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/client/list');

		$this->client->getResponse()->isOk();
	}
}
