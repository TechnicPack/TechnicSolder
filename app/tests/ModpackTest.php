<?php

class ModpackTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testModpackIndex()
	{
		$this->call('GET', '/modpack');

		$this->assertRedirectedTo('/modpack/list');
	}

	public function testModpackList()
	{
		$this->call('GET', '/modpack/list');

		$this->assertResponseOk();
	}

	public function testModpackCreateGet()
	{
		$this->call('GET', '/modpack/create');

		$this->assertResponseOk();
	}

	public function testModpackCreatePostNonUniqueSlug()
	{
		$data = array(
			'name' => 'TestModpack2',
			'slug' => 'testmodpack'
		);

		$response = $this->call('POST', '/modpack/create', $data);
		$this->assertRedirectedTo('/modpack/create');
		$this->assertSessionHasErrors('slug');
	}

	public function testModpackCreatePostNonUniqueName()
	{
		$data = array(
			'name' => 'TestModpack',
			'slug' => 'testmodpack2'
		);

		$response = $this->call('POST', '/modpack/create', $data);
		$this->assertRedirectedTo('/modpack/create');
		$this->assertSessionHasErrors('name');
	}

	public function testModpackCreatePost() 
	{
		$data = array(
			'name' => 'TestModpack2', 
			'slug' => 'testmodpack2'
		);

		$response = $this->call('POST', '/modpack/create', $data);
		$this->assertRedirectedTo('/modpack/view/2');
	}

	public function testModpackDeleteGet()
	{
		$modpack = Modpack::find(1);

		$this->call('GET', '/modpack/delete/'.$modpack->id);

		$this->assertResponseOk();
	}

	public function testModpackDeleteGetInvalidID()
	{
		$this->call('GET', '/modpack/delete/100000');
		$this->assertRedirectedTo('/modpack/list');
	}

	public function testModpackDeletePostInvalidID()
	{
		$this->call('POST', '/modpack/delete/100000');
		$this->assertRedirectedTo('/modpack/list');
	}

	public function testModpackDeletePost()
	{
		$modpack = Modpack::where('slug', '=', 'testmodpack2')->firstOrFail();

		$this->call('POST', '/modpack/delete/'.$modpack->id);
		$this->assertRedirectedTo('/modpack/list');
		$this->assertSessionHas('success');
	}

	public function testModpackBuild()
	{
		$modpack = Modpack::find(1);
		$build = $modpack->builds()->first();

		$this->call('GET', '/modpack/build/'.$build->id);

		$this->assertResponseOk();
	}

	public function testModpackEdit()
	{
		$modpack = Modpack::find(1);

		$this->call('GET', '/modpack/edit/'.$modpack->id);

		$this->assertResponseOk();
	}
}
