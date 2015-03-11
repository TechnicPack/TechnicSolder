<?php

class BuildTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testBuildGet()
	{
		$this->call('GET', '/modpack/build/1');

		$this->assertResponseOk();
	}

	public function testBuildAddGet()
	{
		$this->call('GET', '/modpack/add-build/1');

		$this->assertResponseOk();
	}

	public function testBuildAddPost() 
	{
		$data = array(
			'version' => '1.1.0',
			'minecraft' => '1.7.10:e6b7a531b95d0c172acb704d1f54d1b3'
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/build/2');
	}


	public function testBuildDeleteGet() 
	{
		$build = Build::find(2);

		$response = $this->call('GET', '/modpack/build/'.$build->id.'?action=delete');
		$this->assertResponseOk();
	}

	public function testBuildDeletePost()
	{
		$build = Build::find(2);

		$data = array(
			'confirm-delete' => '1'
		);

		$response = $this->call('POST', '/modpack/build/'.$build->id.'?action=delete', $data);
		$this->assertRedirectedTo('modpack/view/'.$build->modpack->id);
	}
}