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

		$this->assertEquals($build->version, '1.1.0');
		$this->assertEquals($build->minecraft, '1.7.10');
		$this->assertEquals($build->min_memory, '1536');
		$this->assertEquals($build->min_java, '1.7');
	}

	public function testBuildAddPostEmptyVersion()
	{
		$data = array(
			'version' => '',
			'minecraft' => '1.7.10',
			'java-version' => '1.7',
			'memory' => '1536',
			'memory-enabled' => 1
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/add-build/1');
		$this->assertSessionHasErrors('version');
	}

	public function testBuildAddPostEmptyMinecraft()
	{
		$data = array(
			'version' => '1.1.0',
			'minecraft' => '',
			'java-version' => '1.7',
			'memory' => '1536',
			'memory-enabled' => 1
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/add-build/1');
		$this->assertSessionHasErrors('minecraft');
	}

	public function testBuildAddPostEmptyJava()
	{
		$data = array(
			'version' => '1.1.0',
			'minecraft' => '1.7.10',
			'java-version' => '',
			'memory' => '1536',
			'memory-enabled' => 1
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/build/3');

		$build = Build::find(3);

		$this->assertEquals($build->version, '1.1.0');
		$this->assertEquals($build->minecraft, '1.7.10');
		$this->assertEquals($build->min_memory, '1536');
		$this->assertEquals($build->min_java, '');
	}

	public function testBuildAddPostNoMemory()
	{
		$data = array(
			'version' => '1.1.0',
			'minecraft' => '1.7.10',
			'java-version' => '1.7',
			'memory' => 0,
			'memory-enabled' => 0
		);

		$response = $this->call('POST', '/modpack/add-build/1', $data);
		$this->assertRedirectedTo('/modpack/build/4');

		$build = Build::find(4);

		$this->assertEquals($build->version, '1.1.0');
		$this->assertEquals($build->minecraft, '1.7.10');
		$this->assertEquals($build->min_memory, '0');
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
