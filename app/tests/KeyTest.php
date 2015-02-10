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

	public function testKeyCreatePostNonUniqueKey()
	{
		$data = array(
			'name' => 'TestKey2', 
			'api_key' => 'sfIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
		);

		$response = $this->call('POST', '/key/create', $data);
		$this->assertRedirectedTo('/key/create');
		$this->assertSessionHasErrors('api_key');
	}

	public function testKeyCreatePostNonUniqueName()
	{
		$data = array(
			'name' => 'TestKey', 
			'api_key' => 'abIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
		);

		$response = $this->call('POST', '/key/create', $data);
		$this->assertRedirectedTo('/key/create');
		$this->assertSessionHasErrors('name');
	}

	public function testKeyCreatePost() 
	{
		$data = array(
			'name' => 'TestKey2', 
			'api_key' => 'abCvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
		);

		$response = $this->call('POST', '/key/create', $data);
		$this->assertRedirectedTo('/key/list');
		$this->assertSessionHas('success');
	}

	public function testKeyDeleteGet()
	{
		$key = Key::find(1);

		$this->call('GET', '/key/delete/'.$key->id);

		$this->assertResponseOk();
	}

	public function testKeyDeleteGetInvalidID()
	{
		$this->call('GET', '/key/delete/100000');
		$this->assertRedirectedTo('/key/list');
	}

	public function testKeyDeletePostInvalidID()
	{
		$this->call('POST', '/key/delete/100000');
		$this->assertRedirectedTo('/key/list');
	}

	public function testKeyDeletePost()
	{
		$key = Key::where('name', '=', 'TestKey2')->firstOrFail();

		$this->call('POST', '/key/delete/'.$key->id);
		$this->assertRedirectedTo('/key/list');
		$this->assertSessionHas('success');
	}

	public function testKeyList()
	{
		$this->call('GET', '/key/list');

		$this->assertResponseOk();
	}
}
