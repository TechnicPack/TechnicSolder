<?php

class ClientTest extends TestCase {


	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testClientIndex()
	{
		$this->call('GET', '/client');

		$this->assertRedirectedTo('/client/list');
	}

	public function testClientCreateGet()
	{
		$this->call('GET', '/client/create');

		$this->assertResponseOk();
	}

	public function testClientDeleteGet()
	{
		$client = Client::find(1);

		$this->call('GET', '/client/delete/'.$client->id);

		$this->assertResponseOk();
	}

	public function testClientList()
	{
		$this->call('GET', '/client/list');

		$this->assertResponseOk();
	}
}
