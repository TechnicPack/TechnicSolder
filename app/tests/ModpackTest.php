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

	public function testModpackCreateGet()
	{
		$this->call('GET', '/modpack/create');

		$this->assertResponseOk();
	}

	public function testModpackDeleteGet()
	{
		$modpack = Modpack::find(1);

		$this->call('GET', '/modpack/delete/'.$modpack->id);

		$this->assertResponseOk();
	}

	public function testModpackList()
	{
		$this->call('GET', '/modpack/list');

		$this->assertResponseOk();
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
