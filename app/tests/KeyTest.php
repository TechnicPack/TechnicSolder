<?php

class KeyTest extends TestCase {

	public function testKeyIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/key');

		$this->assertRedirectedTo('/key/list');
	}

	public function testKeyCreateGet()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/key/create');

		$this->assertResponseOk();
	}

	public function testKeyDeleteGet()
	{
		$user = User::find(1);
		$key = Key::find(1);
		$this->be($user);

		$response = $this->call('GET', '/key/delete/'.$key->id);

		$this->assertResponseOk();
	}

	public function testKeyList()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/key/list');

		$this->assertResponseOk();
	}
}
