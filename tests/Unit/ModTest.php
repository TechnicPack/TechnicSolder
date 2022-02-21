<?php

namespace Tests\Unit;

use App\Models\Mod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_mod_index()
    {
        $response = $this->get('/mod');

        $response->assertRedirect('/mod/list');
    }

    public function test_mod_list()
    {
        $response = $this->get('/mod/list');

        $response->assertOk();
    }

    public function test_mod_create_get()
    {
        $response = $this->get('/mod/create');

        $response->assertOk();
    }

    public function test_mod_create_post_duplicate_name()
    {
        $data = [
            'pretty_name' => 'TestMod',
            'name' => 'testmod',
        ];

        $response = $this->post('/mod/create', $data);
        $response->assertRedirect('/mod/create');
        $response->assertSessionHasErrors('name');
    }

    public function test_mod_create_post_invalid_link_url()
    {
        $data = [
            'pretty_name' => 'TestMod',
            'name' => 'testmod2',
            'link' => 'solder/io',
        ];

        $response = $this->post('/mod/create', $data);
        $response->assertRedirect('/mod/create');
        $response->assertSessionHasErrors('link');
    }

    public function test_mod_create_post()
    {
        $data = [
            'pretty_name' => 'Random mod name',
            'name' => 'random-mod-name',
            'link' => 'http://technicpack.net',
        ];

        $response = $this->post('/mod/create', $data);
        $response->assertRedirect('/mod/view/3');
    }

    public function test_mod_version_add_post_non_ajax()
    {
        $response = $this->post('/mod/add-version/', [
            'add-version' => 'v1.5.2.v01',
            'add-md5' => '9ece64de3e11a0f15f55ef34f2194760',
            'mod-id' => '2',
        ]);
        $response->assertNotFound();
    }

    public function test_mod_version_add_post_empty_version()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'md5' => '9ece64de3e11a0f15f55ef34f2194760',
                'mod-id' => '2',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
    }

    public function test_mod_version_add_post_empty_mod_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'add-version' => 'v1.5.2.v01',
                'add-md5' => '9ece64de3e11a0f15f55ef34f2194760',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
    }

    public function test_mod_version_add_post_invalid_mod_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'add-version' => 'v1.5.2.v01',
                'add-md5' => '9ece64de3e11a0f15f55ef34f2194760',
                'mod-id' => '1000000',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod from database',
        ]);
    }

    public function test_mod_version_add_post()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'add-version' => '1.7.10-4.0.0',
                'add-md5' => '0925fb5cca71b6e8dd81fac9b257c6d4',
                'mod-id' => '2',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version' => '1.7.10-4.0.0',
            'md5' => '0925fb5cca71b6e8dd81fac9b257c6d4',
            'filesize' => '8.89 KB',
        ]);
    }

    public function test_mod_version_add_post_manual_md5()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'add-version' => '1.7.10-4.0.0',
                'add-md5' => 'butts',
                'mod-id' => '2',
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

    public function test_mod_version_add_post_md5_fail()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version/', [
                'add-version' => 'v1.5.2.1',
                'add-md5' => '',
                'mod-id' => '2',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
        ]);

        if (getenv('REPO_TYPE') === 'remote') {
            $response->assertJson([
                'reason' => 'Remote MD5 failed. URL returned status code - 404',
            ]);
        } else {
            $response->assertJson([
                'reason' => 'Remote MD5 failed. '.config('solder.repo_location').'mods/backtools/backtools-v1.5.2.1.zip is not a valid URI',
            ]);
        }
    }

    public function test_mod_version_rehash_post_non_ajax()
    {
        $response = $this->post('/mod/rehash/', [
            'version-id' => '2',
            'md5' => '9ece64de3e11a0f15f55ef34f2194760',
        ]);
        $response->assertNotFound();
    }

    public function test_mod_version_rehash_post_empty_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash/', [
                'version-id' => '',
                'md5' => '9ece64de3e11a0f15f55ef34f2194760',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Missing Post Data',
        ]);
    }

    public function test_mod_version_rehash_post_invalid_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash/', [
                'version-id' => '10000000',
                'md5' => '9ece64de3e11a0f15f55ef34f2194760',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'reason']);
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod version from database',
        ]);
    }

    public function test_mod_version_rehash_post()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash/', [
                'version-id' => '1',
                'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            'filesize' => '295 bytes',
        ]);
    }

    public function test_mod_version_rehash_post_md5_manual()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash/', [
                'version-id' => '1',
                'md5' => 'butts',
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

    public function test_mod_version_rehash_post_md5_empty()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash/', [
                'version-id' => '1',
                'md5' => '',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            'filesize' => '295 bytes',
        ]);
    }

    public function test_mod_version_delete_non_ajax()
    {
        $response = $this->post('/mod/delete-version/1');
        $response->assertNotFound();
    }

    public function test_mod_version_delete_empty_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/delete-version/', []);

        $response->assertNotFound();
    }

    public function test_mod_version_delete_invalid_id()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/delete-version/10000000', []);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
            'reason' => 'Could not pull mod version from database',
        ]);
    }

    public function test_mod_version_delete()
    {
        //Fake an AJAX call.
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/delete-version/1');

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'version_id' => '1',
            'version' => '1.0',
        ]);
    }

    public function test_mod_delete_get()
    {
        $mod = Mod::find(1);

        $response = $this->get('/mod/delete/'.$mod->id);

        $response->assertOk();
    }

    public function test_mod_delete_get_invalid_id()
    {
        $response = $this->get('/mod/delete/100000');
        $response->assertRedirect('/mod/list');
    }

    public function test_mod_delete_post_invalid_id()
    {
        $response = $this->post('/mod/delete/100000');
        $response->assertRedirect('/mod/list');
    }

    public function test_mod_delete_post()
    {
        $modpack = Mod::where('name', 'backtools')->firstOrFail();

        $response = $this->post('/mod/delete/'.$modpack->id);
        $response->assertRedirect('/mod/list');
        $response->assertSessionHas('success');
    }
}
