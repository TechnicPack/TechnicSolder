<?php

namespace Tests\Unit;

use App\Mod;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModTest extends TestCase
{

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

        $response = $this->post('/mod/create', $data);
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

        $response = $this->post('/mod/create', $data);
        $response->assertRedirect('/mod/create');
        $response->assertSessionHasErrors('link');
    }

    public function testModCreatePost()
    {
        $data = [
            'pretty_name' => 'Random mod name',
            'name' => 'random-mod-name',
            'link' => 'http://technicpack.net',
        ];

        $response = $this->post('/mod/create', $data);
        $response->assertRedirect('/mod/view/3');
    }

    public function testModVersionAddPostNonAjax()
    {
        $response = $this->post('/mod/add-version/', [
            "add-version" => "v1.5.2.v01",
            "add-md5" => "9ece64de3e11a0f15f55ef34f2194760",
            "mod-id" => "2"
        ]);
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
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                "add-version" => "v1.5.2.v01",
                "add-md5" => "9ece64de3e11a0f15f55ef34f2194760"
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
    }

    public function testModVersionAddPostInvalidModID()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/add-version/', [
                "add-version" => "v1.5.2.v01",
                "add-md5" => "9ece64de3e11a0f15f55ef34f2194760",
                "mod-id" => "1000000"
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod from database',
        ]);
    }

    public function testModVersionAddPost()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/add-version/', [
                "add-version" => "1.7.10-4.0.0",
                "add-md5" => "0925fb5cca71b6e8dd81fac9b257c6d4",
                "mod-id" => "2"
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version' => '1.7.10-4.0.0',
            'md5' => '0925fb5cca71b6e8dd81fac9b257c6d4',
            'filesize' => '8.89 KB',
        ]);
    }

    public function testModVersionAddPostManualMD5()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/add-version/', [
                "add-version" => "1.7.10-4.0.0",
                "add-md5" => "butts",
                "mod-id" => "2"
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'warning',
            'version' => '1.7.10-4.0.0',
            'md5' => 'butts',
            'filesize' => '8.89 KB',
            'reason' => 'MD5 provided does not match file MD5: 0925fb5cca71b6e8dd81fac9b257c6d4',
        ]);
    }

    public function testModVersionAddPostMD5Fail()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/add-version/', [
                "add-version" => "v1.5.2.1",
                "add-md5" => "",
                "mod-id" => "2"
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
        ]);

        if (getenv('REPO_TYPE') === 'remote') {
            $response->assertJson([
                'reason' => 'Remote MD5 failed. URL returned status code - 404'
            ]);
        } else {
            $response->assertJson([
                'reason' => 'Remote MD5 failed. ' . config('solder.repo_location') . 'mods/backtools/backtools-v1.5.2.1.zip is not a valid URI'
            ]);
        }
    }

    public function testModVersionRehashPostNonAjax()
    {
        $response = $this->post('/mod/rehash/', [
            "version-id" => "2",
            "md5" => "9ece64de3e11a0f15f55ef34f2194760"
        ]);
        $response->assertNotFound();
    }

    public function testModVersionRehashPostEmptyID()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/rehash/', [
                "version-id" => "",
                "md5" => "9ece64de3e11a0f15f55ef34f2194760"
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
    }

    public function testModVersionRehashPostInvalidID()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/rehash/', [
                "version-id" => "10000000",
                "md5" => "9ece64de3e11a0f15f55ef34f2194760"
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod version from database',
        ]);
    }

    public function testModVersionRehashPost()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/rehash/', [
                "version-id" => "1",
                "md5" => "bdbc6c6cc48c7b037e4aef64b58258a3"
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            'filesize' => '295 bytes',
        ]);
    }

    public function testModVersionRehashPostMD5Manual()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/rehash/', [
                "version-id" => "1",
                "md5" => "butts"
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'warning',
            'version_id' => '1',
            'md5' => 'butts',
            'filesize' => '295 bytes',
            'reason' => 'MD5 provided does not match file MD5: bdbc6c6cc48c7b037e4aef64b58258a3',
        ]);
    }

    public function testModVersionRehashPostMD5Empty()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/rehash/', [
                "version-id" => "1",
                "md5" => ""
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            'filesize' => '295 bytes',
        ]);
    }

    public function testModVersionDeleteNonAjax()
    {
        $response = $this->post('/mod/delete-version/1');
        $response->assertNotFound();
    }

    public function testModVersionDeleteEmptyID()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/delete-version/', []);

        $response->assertNotFound();
    }

    public function testModVersionDeleteInvalidID()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/delete-version/10000000', []);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod version from database',
        ]);
    }

    public function testModVersionDelete()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(["X-Requested-With" => "XMLHttpRequest"])
            ->post('/mod/delete-version/1');

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'version' => '1.0',
        ]);
    }

    public function testModDeleteGet()
    {
        $mod = Mod::find(1);

        $response = $this->get('/mod/delete/' . $mod->id);

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
        $modpack = Mod::where('name', 'backtools')->firstOrFail();

        $response = $this->post('/mod/delete/' . $modpack->id);
        $response->assertRedirect('/mod/list');
        $response->assertSessionHas('success');
    }
}
