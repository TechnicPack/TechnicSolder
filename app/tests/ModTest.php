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
			'pretty_name' => 'TestMod',
			'name' => 'testmod2',
			'link' => 'http://solder.io',
			'donatelink' => 'http://solder.io'
		);

		$response = $this->call('POST', '/mod/create', $data);
		$this->assertRedirectedTo('/mod/view/2');
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
		$modpack = Mod::where('name', '=', 'testmod2')->firstOrFail();

		$this->call('POST', '/mod/delete/'.$modpack->id);
		$this->assertRedirectedTo('/mod/list');
		$this->assertSessionHas('success');
	}

	public function testModVersionAddPostNonAjax()
	{
		$this->call('POST', '/mod/add-version/', array("add-version"=>"1.0.0","add-md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3","mod-id"=>"1"));
		$this->assertResponseStatus(404);
	}

	public function testModVersionAddPostEmptyVersion()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3","mod-id"=>"1"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Missing Post Data');
	}

	public function testModVersionAddPostEmptyModID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.0.0","add-md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Missing Post Data');
	}

	public function testModVersionAddPostInvalidModID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.0.0","add-md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3","mod-id"=>"1000000"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Could not pull mod from database');
	}

	public function testModVersionAddPost()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.0.0","add-md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3","mod-id"=>"1"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'warning');
		$this->assertTrue($json['version'] == '1.0.0');
		$this->assertTrue($json['md5'] == 'bdbc6c6cc48c7b037e4aef64b58258a3');
		$this->assertTrue($json['reason'] == 'MD5 provided does not match file MD5: Null');
	}

	public function testModVersionAddPostMD5Fail()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/add-version/', array("add-version"=>"1.0.0","add-md5"=>"","mod-id"=>"1"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Remote MD5 failed. See app/storage/logs for more details');
	}

	public function testModVersionRehashPostNonAjax()
	{
		$this->call('POST', '/mod/rehash/', array("version-id"=>"2","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"));
		$this->assertResponseStatus(404);
	}

	public function testModVersionRehashPostEmptyID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Missing Post Data');
	}

	public function testModVersionRehashPostInvalidID()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"10000000","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Could not pull mod version from database');
	}

	public function testModVersionRehashPost()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"2","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('md5', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'warning');
		$this->assertTrue($json['version_id'] == '2');
		$this->assertTrue($json['md5'] == 'bdbc6c6cc48c7b037e4aef64b58258a3');
		$this->assertTrue($json['reason'] == 'MD5 provided does not match file MD5: Null');
	}

	public function testModVersionRehashPostMD5Fail()
	{
		//Fake an AJAX call.
		$response = $this->call('POST', '/mod/rehash/', array("version-id"=>"2","md5"=>""),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('reason', $json));
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Remote MD5 failed. See app/storage/logs for more details');
	}

	public function testModVersionDeleteNonAjax()
	{
		$this->call('GET', '/mod/delete-version/2');
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
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Missing Post Data');
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
		$this->assertTrue($json['status'] == 'error');
		$this->assertTrue($json['reason'] == 'Could not pull mod version from database');
	}

	public function testModVersionDelete()
	{
		//Fake an AJAX call.
		$response = $this->call('GET', '/mod/delete-version/2', array(),
						array(), array("HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"));

		$this->assertResponseOk();
		$this->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$this->assertTrue(array_key_exists('status', $json));
		$this->assertTrue(array_key_exists('version_id', $json));
		$this->assertTrue(array_key_exists('version', $json));
		$this->assertTrue($json['status'] == 'success');
		$this->assertTrue($json['version_id'] == '2');
		$this->assertTrue($json['version'] == '1.0.0');
	}
}
