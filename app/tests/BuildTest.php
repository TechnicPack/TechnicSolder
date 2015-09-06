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
			'minecraft' => '1.7.10',
			'java-version' => '1.7',
			'memory' => '1536',
			'memory-enabled' => 1
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/build/2');

		$build = Build::find(2);

		$this->assertEquals($build->min_memory, '1536');
		$this->assertEquals($build->min_java, '1.7');
	}

	public function testBuildEditGet()
	{
		$build = Build::find(2);

		$this->call('GET', '/modpack/build/'.$build->id.'?action=edit');
		$this->assertResponseOk();
	}

	public function testBuildEditPost()
	{
		$build = Build::find(2);

		$data = array(
			'confirm-edit' => '1',
			'version' => '1.1.0',
			'minecraft' => '1.7.10',
			'java-version' => '1.8',
			'memory' => '1024',
			'memory-enabled' => '1'
		);

		$response = $this->call('POST', '/modpack/build/'.$build->id.'?action=edit', $data);
		$this->assertRedirectedTo('/modpack/build/2');

		$build = Build::find(2);

		$this->assertEquals($build->min_memory, '1024');
		$this->assertEquals($build->min_java, '1.8');
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
