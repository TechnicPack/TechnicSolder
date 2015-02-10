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

	public function testClientCreatePostUnUniqueUUID()
	{
		$data = array(
			'name' => 'TestClient2', 
			'uuid' => '2ezf6f26-eb15-4ccb-9f0b-8z5ed2c72946'
		);

		$response = $this->call('POST', '/client/create', $data);
		$this->assertRedirectedTo('/client/create');
		$this->assertSessionHasErrors('uuid');
	}

	public function testClientCreatePostUnUniqueName()
	{
		$data = array(
			'name' => 'TestClient', 
			'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946'
		);

		$response = $this->call('POST', '/client/create', $data);
		$this->assertRedirectedTo('/client/create');
		$this->assertSessionHasErrors('name');
	}

	public function testClientCreatePost() 
	{
		$data = array(
			'name' => 'TestClient2', 
			'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946'
		);

		$response = $this->call('POST', '/client/create', $data);
		$this->assertRedirectedTo('/client/list');
		$this->assertSessionHas('success');
	}

	public function testClientDeleteGet()
	{
		$client = Client::find(1);

		$this->call('GET', '/client/delete/'.$client->id);

		$this->assertResponseOk();
	}

	public function testClientDeleteGetInvalidID()
	{
		$this->call('GET', '/client/delete/100000');
		$this->assertRedirectedTo('/client/list');
	}

	public function testClientDeletePostInvalidID()
	{
		$this->call('POST', '/client/delete/100000');
		$this->assertRedirectedTo('/client/list');
	}

	public function testClientDeletePost()
	{
		$client = Client::where('name', '=', 'TestClient2')->firstOrFail();

		$this->call('POST', '/client/delete/'.$client->id);
		$this->assertRedirectedTo('/client/list');
		$this->assertSessionHas('success');
	}

	public function testClientList()
	{
		$this->call('GET', '/client/list');

		$this->assertResponseOk();
	}
}
