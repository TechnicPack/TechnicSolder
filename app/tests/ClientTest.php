<?php

class ClientTest extends TestCase {

	public function testClientIndex()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/client');

		$this->assertRedirectedTo('/client/list');
	}

	public function testClientCreateGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/client/create');

		$this->assertResponseOk();
	}

	public function testClientDeleteGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$client = Client::find(1);
		$this->be($user);

		$this->call('GET', '/client/delete/'.$client->id);

		$this->assertResponseOk();
	}

	public function testClientList()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/client/list');

		$this->assertResponseOk();
	}
}
