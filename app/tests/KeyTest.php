<?php

class KeyTest extends TestCase {

	public function testKeyIndex()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/key');

		$this->assertRedirectedTo('/key/list');
	}

	public function testKeyCreateGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/key/create');

		$this->assertResponseOk();
	}

	public function testKeyDeleteGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$key = Key::find(1);
		$this->be($user);

		$this->call('GET', '/key/delete/'.$key->id);

		$this->assertResponseOk();
	}

	public function testKeyList()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/key/list');

		$this->assertResponseOk();
	}
}
