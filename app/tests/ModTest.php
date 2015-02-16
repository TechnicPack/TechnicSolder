<?php

class ModTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testModIndex()
	{
		$this->call('GET', '/mod');

		$this->assertRedirectedTo('/mod/list');
	}

	public function testModList()
	{
		$this->call('GET', '/mod/list');

		$this->assertResponseOk();
	}

	public function testModCreateGet()
	{
		$this->call('GET', '/mod/create');

		$this->assertResponseOk();
	}

	public function testModCreatePostNonUniqueName()
	{
		$data = array(
			'pretty_name' => 'TestMod',
			'name' => 'testmod'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/create');
		$this->assertSessionHasErrors('name');
	}

	public function testModCreatePostInvalidLinkURL()
	{
		$data = array(
			'pretty_name' => 'TestMod',
			'name' => 'testmod2',
			'link' => 'solder/io'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/create');
		$this->assertSessionHasErrors('link');
	}

	public function testModCreatePostInvalidDonateURL()
	{
		$data = array(
			'pretty_name' => 'TestMod',
			'name' => 'testmod2',
			'donatelink' => 'solder/io'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/create');
		$this->assertSessionHasErrors('donatelink');
	}

	public function testModCreatePost() 
	{
		$data = array(
			'pretty_name' => 'TestMod',
			'name' => 'testmod2',
			'link' => 'http://solder.io',
			'donatelink' => 'http://solder.io'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/view/2');
	}

	public function testModDeleteGet()
	{
		$mod = Mod::find(1);

		$this->call('GET', '/mod/delete/'.$mod->id);

		$this->assertResponseOk();
	}

	public function testModDeleteGetInvalidID()
	{
		$this->call('GET', '/mod/delete/100000');
		$this->assertRedirectedTo('/mod/list');
	}

	public function testModDeletePostInvalidID()
	{
		$this->call('POST', '/mod/delete/100000');
		$this->assertRedirectedTo('/mod/list');
	}

	public function testModDeletePost()
	{
		$modpack = Mod::where('name', '=', 'testmod2')->firstOrFail();

		$this->call('POST', '/mod/delete/'.$modpack->id);
		$this->assertRedirectedTo('/mod/list');
		$this->assertSessionHas('success');
	}
}