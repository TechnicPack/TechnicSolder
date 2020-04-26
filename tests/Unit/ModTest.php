<?php

namespace Tests\Unit;

use App\Mod;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModTest extends TestCase {

    use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();

        $this->seed();

		$user = User::find(1);
		$this->be($user);
	}

	public function testModIndex()
	{
		$response = $this->get('/mod');

		$response->assertRedirect('/mod/list');
	}

	public function testModList()
	{
		$response = $this->get('/mod/list');

		$response->assertOk();
	}

	public function testModCreateGet()
	{
		$response = $this->get('/mod/create');

		$response->assertOk();
	}

	public function testModCreatePostNonUniqueName()
	{
		$data = [
			'pretty_name' => 'TestMod',
			'name' => 'testmod'
        ];

		$response = $response = $this->post('/mod/create', $data);
		$response->assertRedirect('/mod/create');
		$response->assertSessionHasErrors('name');
	}

	public function testModCreatePostInvalidLinkURL()
	{
		$data = [
			'pretty_name' => 'TestMod',
			'name' => 'testmod2',
			'link' => 'solder/io'
        ];

		$response = $response = $this->post('/mod/create', $data);
		$response->assertRedirect('/mod/create');
		$response->assertSessionHasErrors('link');
	}

	public function testModCreatePost()
	{
		$data = [
			'pretty_name' => 'Backtools',
			'name' => 'backtools',
			'link' => 'http://solder.io',
        ];

		$response = $response = $this->post('/mod/create', $data);
		$response->assertRedirect('/mod/view/2');
	}

	public function testModVersionAddPostNonAjax()
	{
		$response = $this->post('/mod/add-version/', ["add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760","mod-id"=>"2"]);
		$response->assertNotFound();
	}

	public function testModVersionAddPostEmptyVersion()
	{
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/add-version/', [
                "md5" => "9ece64de3e11a0f15f55ef34f2194760",
                "mod-id" => "2"
            ]);

		$response->assertOk();
		$response->assertJsonStructure(['status', 'reason']);
		$response->assertJson([
		    'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
	}

	public function testModVersionAddPostEmptyModID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/add-version/', ["add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionAddPostInvalidModID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/add-version/', ["add-version"=>"v1.5.2.v01","add-md5"=>"9ece64de3e11a0f15f55ef34f2194760","mod-id"=>"1000000"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Could not pull mod from database', $json['reason']);
	}

	public function testModVersionAddPost()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/add-version/', ["add-version"=>"1.7.10-4.0.0","add-md5"=>"0925fb5cca71b6e8dd81fac9b257c6d4","mod-id"=>"2"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version', $json));
		$response->assertTrue(array_key_exists('filesize', $json));
		$response->assertTrue(array_key_exists('md5', $json));
		$response->assertEquals('success', $json['status']);
		$response->assertEquals('1.7.10-4.0.0', $json['version']);
		$response->assertEquals('0925fb5cca71b6e8dd81fac9b257c6d4', $json['md5']);
		$response->assertEquals('0.01 MB', $json['filesize']);
	}

	public function testModVersionAddPostManualMD5()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/add-version/', ["add-version"=>"1.7.10-4.0.0","add-md5"=>"butts","mod-id"=>"2"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version', $json));
		$response->assertTrue(array_key_exists('filesize', $json));
		$response->assertTrue(array_key_exists('md5', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('warning', $json['status']);
		$response->assertEquals('1.7.10-4.0.0', $json['version']);
		$response->assertEquals('butts', $json['md5']);
		$response->assertEquals('0.01 MB', $json['filesize']);
		$response->assertEquals('MD5 provided does not match file MD5: 0925fb5cca71b6e8dd81fac9b257c6d4', $json['reason']);
	}

	public function testModVersionAddPostMD5Fail()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/add-version/', ["add-version"=>"v1.5.2.1","add-md5"=>"","mod-id"=>"2"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		if(getenv('REPO_TYPE') == 'remote') {
			$response->assertEquals('Remote MD5 failed. URL returned status code - 404', $json['reason']);
		} else {
			$response->assertEquals('Remote MD5 failed. ' . getenv('REPO') . 'mods/backtools/backtools-v1.5.2.1.zip is not a valid URI', $json['reason']);
		}
	}

	public function testModVersionRehashPostNonAjax()
	{
		$response = $this->post('/mod/rehash/', ["version-id"=>"2","md5"=>"9ece64de3e11a0f15f55ef34f2194760"]);
		$response->assertResponseStatus(404);
	}

	public function testModVersionRehashPostEmptyID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/rehash/', ["version-id"=>"","md5"=>"9ece64de3e11a0f15f55ef34f2194760"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionRehashPostInvalidID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/rehash/', ["version-id"=>"10000000","md5"=>"9ece64de3e11a0f15f55ef34f2194760"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Could not pull mod version from database', $json['reason']);
	}

	public function testModVersionRehashPost()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/rehash/', ["version-id"=>"1","md5"=>"bdbc6c6cc48c7b037e4aef64b58258a3"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version_id', $json));
		$response->assertTrue(array_key_exists('filesize', $json));
		$response->assertTrue(array_key_exists('md5', $json));
		$response->assertEquals('success', $json['status']);
		$response->assertEquals('1', $json['version_id']);
		$response->assertEquals('bdbc6c6cc48c7b037e4aef64b58258a3', $json['md5']);
		$response->assertEquals('0.00 MB', $json['filesize']);
	}

	public function testModVersionRehashPostMD5Manual()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/rehash/', ["version-id"=>"1","md5"=>"butts"],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version_id', $json));
		$response->assertTrue(array_key_exists('filesize', $json));
		$response->assertTrue(array_key_exists('md5', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('warning', $json['status']);
		$response->assertEquals('1', $json['version_id']);
		$response->assertEquals('butts', $json['md5']);
		$response->assertEquals('0.00 MB', $json['filesize']);
		$response->assertEquals('MD5 provided does not match file MD5: bdbc6c6cc48c7b037e4aef64b58258a3', $json['reason']);
	}

	public function testModVersionRehashPostMD5Empty()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/rehash/', ["version-id"=>"1","md5"=>""],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version_id', $json));
		$response->assertTrue(array_key_exists('filesize', $json));
		$response->assertTrue(array_key_exists('md5', $json));
		$response->assertEquals('success', $json['status']);
		$response->assertEquals('1', $json['version_id']);
		$response->assertEquals('bdbc6c6cc48c7b037e4aef64b58258a3', $json['md5']);
		$response->assertEquals('0.00 MB', $json['filesize']);
	}

	public function testModVersionDeleteNonAjax()
	{
		$response = $this->get('/mod/delete-version/3');
		$response->assertResponseStatus(404);
	}

	public function testModVersionDeleteEmptyID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/delete-version/', [],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Missing Post Data', $json['reason']);
	}

	public function testModVersionDeleteInvalidID()
	{
		//Fake an AJAX call.
		$response = $response = $this->post('/mod/delete-version/10000000', [],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('reason', $json));
		$response->assertEquals('error', $json['status']);
		$response->assertEquals('Could not pull mod version from database', $json['reason']);
	}

	public function testModVersionDelete()
	{
		//Fake an AJAX call.
		$response = $response = $this->get('/mod/delete-version/3', [],
						[], ["HTTP_X_REQUESTED_WITH"=>"XMLHttpRequest"]);

		$response->assertOk();
		$response->assertTrue(is_a($response,'Illuminate\Http\JsonResponse'));
		$json = $response->getData(true);

		$response->assertTrue(array_key_exists('status', $json));
		$response->assertTrue(array_key_exists('version_id', $json));
		$response->assertTrue(array_key_exists('version', $json));
		$response->assertEquals('success', $json['status']);
		$response->assertEquals('3', $json['version_id']);
		$response->assertEquals('1.7.10-4.0.0', $json['version']);
	}

	public function testModDeleteGet()
	{
		$mod = Mod::find(1);

		$response = $this->get('/mod/delete/'.$mod->id);

		$response->assertOk();
	}

	public function testModDeleteGetInvalidID()
	{
		$response = $this->get('/mod/delete/100000');
		$response->assertRedirect('/mod/list');
	}

	public function testModDeletePostInvalidID()
	{
		$response = $this->post('/mod/delete/100000');
		$response->assertRedirect('/mod/list');
	}

	public function testModDeletePost()
	{
		$modpack = Mod::where('name', '=', 'backtools')->firstOrFail();

		$response = $this->post('/mod/delete/'.$modpack->id);
		$response->assertRedirect('/mod/list');
		$response->assertSessionHas('success');
	}
}
