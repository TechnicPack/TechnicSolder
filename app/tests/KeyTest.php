<?php

class KeyTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testKeyIndex()
	{
		$this->call('GET', '/key');

		$this->assertRedirectedTo('/key/list');
	}

	public function testKeyCreateGet()
	{
		$this->call('GET', '/key/create');

		$this->assertResponseOk();
	}

	public function testKeyDeleteGet()
	{
		$key = Key::find(1);

		$this->call('GET', '/key/delete/'.$key->id);

		$this->assertResponseOk();
	}

	public function testKeyList()
	{
		$this->call('GET', '/key/list');

		$this->assertResponseOk();
	}
}
