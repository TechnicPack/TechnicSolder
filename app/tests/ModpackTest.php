<?php

class ModpackTest extends TestCase {

	public function testModpackIndex()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/modpack');

		$this->assertRedirectedTo('/modpack/list');
	}

	public function testModpackCreateGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/modpack/create');

		$this->assertResponseOk();
	}

	public function testModpackDeleteGet()
	{
		Route::enableFilters();
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$this->be($user);

		$this->call('GET', '/modpack/delete/'.$modpack->id);

		$this->assertResponseOk();
	}

	public function testModpackList()
	{
		Route::enableFilters();
		$user = User::find(1);
		$this->be($user);

		$this->call('GET', '/modpack/list');

		$this->assertResponseOk();
	}

	public function testModpackBuild()
	{
		Route::enableFilters();
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$build = $modpack->builds()->first();
		$this->be($user);

		$this->call('GET', '/modpack/build/'.$build->id);

		$this->assertResponseOk();
	}

	public function testModpackEdit()
	{
		Route::enableFilters();
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$this->be($user);

		$this->call('GET', '/modpack/edit/'.$modpack->id);

		$this->assertResponseOk();
	}
}
