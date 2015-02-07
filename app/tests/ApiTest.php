<?php

class ApiTest extends TestCase {

	public function testBase()
	{
		$response = $this->call('GET', 'api/');
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));

		$this->assertTrue($this->client->getResponse()->isOk());
		$this->assertEquals('{"api":"TechnicSolder","version":"'.SOLDER_VERSION.'","stream":"'.SOLDER_STREAM.'"}', $response->getContent());
	}

	public function testModpack()
	{
		$response = $this->call('GET', 'api/modpack/');
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue($this->client->getResponse()->isOk());
		$this->assertTrue(array_key_exists('modpacks', $json));
		$this->assertTrue(array_key_exists('mirror_url', $json));
	}

	public function testMod()
	{
		$response = $this->call('GET', 'api/mod/');
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue($this->client->getResponse()->isOk());
		$this->assertTrue(array_key_exists('error', $json));
	}

	public function testModpackSlug()
	{
		$response = $this->call('GET', 'api/modpack/');
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue($this->client->getResponse()->isOk());
		$this->assertTrue(array_key_exists('modpacks', $json));
	}
}
