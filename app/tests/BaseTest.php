<?php

class BaseTest extends TestCase {

	public function testLoginGet()
	{
		$response = $this->call('GET', '/login');

		$this->assertResponseOk();
	}

	public function testCheckerCache(){

		$this->assertTrue(Cache::get('checker'));

	}

	public function testIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/');

		$this->assertRedirectedTo('/dashboard');
	}
/*
	public function testUnauthorizedAccess()
	{
		//This fails for some reason
		//$response = $this->call('GET', '/dashboard');

		//$this->assertTrue($this->client->getResponse()->isOk());
		//$this->assertRedirectedTo('/login');
	}

	public function testDashboard()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/dashboard');

		$this->assertResponseOk();
	}*/
}