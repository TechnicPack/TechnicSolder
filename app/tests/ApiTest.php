<?php

class ApiTest extends TestCase {

	public function testBase()
	{
		$response = $this->call('GET', 'api/');
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));

		$this->assertEquals('{"api":"TechnicSolder","version":"'.SOLDER_VERSION.'","stream":"'.SOLDER_STREAM.'"}', $response->getContent());
	}

	public function testModpack()
	{
		$response = $this->call('GET', 'api/modpack/');
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('modpacks', $json));
		$this->assertTrue(array_key_exists('mirror_url', $json));
	}

	public function testMod()
	{
		$response = $this->call('GET', 'api/mod/');
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('mods', $json));
	}

	public function testInvalidModpack()
	{
		$response = $this->call('GET', 'api/modpack/bob');
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('error', $json));
	}

	public function testModpackSlug()
	{
		$modpack = Modpack::find(1);
		$response = $this->call('GET', 'api/modpack/'.$modpack->slug);
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('name', $json));
		$this->assertTrue(array_key_exists('display_name', $json));
		$this->assertTrue(array_key_exists('url', $json));
		$this->assertTrue(array_key_exists('icon', $json));
		$this->assertTrue(array_key_exists('icon_md5', $json));
		$this->assertTrue(array_key_exists('latest', $json));
		$this->assertTrue(array_key_exists('logo', $json));
		$this->assertTrue(array_key_exists('logo_md5', $json));
		$this->assertTrue(array_key_exists('recommended', $json));
		$this->assertTrue(array_key_exists('background', $json));
		$this->assertTrue(array_key_exists('background_md5', $json));
		$this->assertTrue(array_key_exists('builds', $json));
	}

	public function testInvalidMod()
	{
		$response = $this->call('GET', 'api/mod/bob');
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('error', $json));
	}

	public function testModSlug()
	{
		$mod = Mod::find(1);
		$response = $this->call('GET', 'api/mod/'.$mod->name);
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('name', $json));
		$this->assertTrue(array_key_exists('pretty_name', $json));
		$this->assertTrue(array_key_exists('author', $json));
		$this->assertTrue(array_key_exists('description', $json));
		$this->assertTrue(array_key_exists('link', $json));
		$this->assertTrue(array_key_exists('donate', $json));
		$this->assertTrue(array_key_exists('versions', $json));
	}

	public function testModpackBuild()
	{
		$modpack = Modpack::find(1);
		$build = $modpack->builds->first();
		$response = $this->call('GET', 'api/modpack/'.$modpack->slug.'/'.$build->version);
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('minecraft', $json));
		$this->assertTrue(array_key_exists('forge', $json));
		$this->assertTrue(array_key_exists('java', $json));
		$this->assertTrue(array_key_exists('memory', $json));
		$this->assertTrue(array_key_exists('mods', $json));
	}

	public function testModVersion()
	{
		$mod = Mod::find(1);
		$modversion = $mod->versions->first();
		$response = $this->call('GET', 'api/mod/'.$mod->name.'/'.$modversion->version);
		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('url', $json));
	}
}
