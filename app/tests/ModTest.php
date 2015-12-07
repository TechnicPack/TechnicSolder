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
			'pretty_name' => 'Backtools',
			'name' => 'backtools',
			'link' => 'http://solder.io',
			'donatelink' => 'http://solder.io'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/view/2');
	}

	public function testModVersionAddPostNonAjax()
	{
		$this->call('POST', '/mod/add-version/', array("add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760","mod-id"=>"2"));
		$this->assertResponseStatus(404);
	}

	public function testModVersionAddPostEmptyVersion()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("md5"=>"9ece64de3e11a0f15f55ef34f2194760","mod-id"=>"2"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionAddPostEmptyModID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionAddPostInvalidModID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760","mod-id"=>"1000000"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Could not pull mod from database', $json['reason']);
	}

	public function testModVersionAddPost()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.7.10-4.0.0","add-md5"=>"0925fb5cca71b6e8dd81fac9b257c6d4","mod-id"=>"2"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertEquals('success', $json['status']);
		$this->assertEquals('1.7.10-4.0.0', $json['version']);
		$this->assertEquals('0925fb5cca71b6e8dd81fac9b257c6d4', $json['md5']);
		$this->assertEquals('0.01 MB', $json['filesize']);
	}

	public function testModVersionAddPostManualMD5()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.7.10-4.0.0","add-md5"=>"butts","mod-id"=>"2"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('warning', $json['status']);
		$this->assertEquals('1.7.10-4.0.0', $json['version']);
		$this->assertEquals('butts', $json['md5']);
		$this->assertEquals('0.01 MB', $json['filesize']);
		$this->assertEquals('MD5 provided does not match file MD5: 0925fb5cca71b6e8dd81fac9b257c6d4', $json['reason']);
	}

	public function testModVersionAddPostMD5Fail()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"v1.5.2.1","add-md5"=>"","mod-id"=>"2"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		if(getenv('REPO_TYPE') == 'remote') {
			$this->assertEquals('Remote MD5 failed. URL returned status code - 404', $json['reason']);
		} else {
			$this->assertEquals('Remote MD5 failed. ' . getenv('REPO') . 'mods/backtools/backtools-v1.5.2.1.zip is not a valid URI', $json['reason']);
		}
	}

	public function testModVersionRehashPostNonAjax()
	{
		$this->call('POST', '/mod/rehash/', array("version-id"=>"2","md5"=>"9ece64de3e11a0f15f55ef34f2194760"));
		$this->assertResponseStatus(404);
	}

	public function testModVersionRehashPostEmptyID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"","md5"=>"9ece64de3e11a0f15f55ef34f2194760"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionRehashPostInvalidID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"10000000","md5"=>"9ece64de3e11a0f15f55ef34f2194760"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Could not pull mod version from database', $json['reason']);
	}

	public function testModVersionRehashPost()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"1","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertEquals('success', $json['status']);
		$this->assertEquals('1', $json['version_id']);
		$this->assertEquals('bdbc6c6cc48c7b037e4aef64b58258a3', $json['md5']);
		$this->assertEquals('0.00 MB', $json['filesize']);
	}

	public function testModVersionRehashPostMD5Manual()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"1","md5"=>"butts"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('warning', $json['status']);
		$this->assertEquals('1', $json['version_id']);
		$this->assertEquals('butts', $json['md5']);
		$this->assertEquals('0.00 MB', $json['filesize']);
		$this->assertEquals('MD5 provided does not match file MD5: bdbc6c6cc48c7b037e4aef64b58258a3', $json['reason']);
	}

	public function testModVersionRehashPostMD5Empty()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"1","md5"=>""),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('filesize', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertEquals('success', $json['status']);
		$this->assertEquals('1', $json['version_id']);
		$this->assertEquals('bdbc6c6cc48c7b037e4aef64b58258a3', $json['md5']);
		$this->assertEquals('0.00 MB', $json['filesize']);
	}

	public function testModVersionDeleteNonAjax()
	{
		$this->call('GET', '/mod/delete-version/3');
		$this->assertResponseStatus(404);
	}

	public function testModVersionDeleteEmptyID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/delete-version/', array(),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionDeleteInvalidID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/delete-version/10000000', array(),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertEquals('error', $json['status']);
		$this->assertEquals('Could not pull mod version from database', $json['reason']);
	}

	public function testModVersionDelete()
	{
		//Fake an AJAX call.
		$response = $this->call('GET', '/mod/delete-version/3', array(),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('version', $json));
		$this->assertEquals('success', $json['status']);
		$this->assertEquals('3', $json['version_id']);
		$this->assertEquals('1.7.10-4.0.0', $json['version']);
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
		$modpack = Mod::where('name', '=', 'backtools')->firstOrFail();

		$this->call('POST', '/mod/delete/'.$modpack->id);
		$this->assertRedirectedTo('/mod/list');
		$this->assertSessionHas('success');
	}
}
