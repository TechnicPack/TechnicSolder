<?php

class ModpackTest extends TestCase {

	public function testModpackIndex()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/modpack');

		$this->assertRedirectedTo('/modpack/list');
	}

	public function testModpackCreateGet()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/modpack/create');

		$this->client->getResponse()->isOk();
	}

	public function testModpackDeleteGet()
	{
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$this->be($user);

		$response = $this->call('GET', '/modpack/delete/'.$modpack->id);

		$this->client->getResponse()->isOk();
	}

	public function testModpackList()
	{
		$user = User::find(1);
		$this->be($user);

		$response = $this->call('GET', '/modpack/list');

		$this->client->getResponse()->isOk();
	}

	public function testModpackBuild()
	{
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$build = $modpack->builds()->first();
		$this->be($user);

		$response = $this->call('GET', '/modpack/build/'.$build->id);

		$this->client->getResponse()->isOk();
	}

	public function testModpackEdit()
	{
		$user = User::find(1);
		$modpack = Modpack::find(1);
		$this->be($user);

		$response = $this->call('GET', '/modpack/edit/'.$modpack->id);

		$this->client->getResponse()->isOk();
	}
}
