<?php

namespace Tests\Unit;

use App\Models\Mod;
use App\Models\Modpack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_base()
    {
        $response = $this->get('api/');
        $response->assertOk();
        $response->assertJson([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM,
        ]);
    }

    public function test_modpack()
    {
        $response = $this->get('api/modpack');
        $response->assertOk();
        $response->assertJsonStructure(['modpacks', 'mirror_url']);
    }

    public function test_mod()
    {
        $response = $this->get('api/mod');
        $response->assertOk();
        $response->assertJsonStructure(['mods']);
    }

    public function test_invalid_modpack()
    {
        $response = $this->get('api/modpack/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_modpack_slug()
    {
        $modpack = Modpack::find(1);
        $response = $this->get('api/modpack/'.$modpack->slug);
        $response->assertOk();
        $response->assertJsonStructure([
            'name',
            'display_name',
            'url',
            'icon',
            'icon_md5',
            'latest',
            'logo',
            'logo_md5',
            'recommended',
            'background',
            'background_md5',
            'builds',
        ]);
        $response->assertJsonPath('builds', ['1.0.0'], true);
    }

    public function test_invalid_mod()
    {
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);
    }

    public function test_mod_slug()
    {
        $mod = Mod::find(1);
        $response = $this->get('api/mod/'.$mod->name);
        $response->assertOk();
        $response->assertJsonStructure([
            'name',
            'pretty_name',
            'author',
            'description',
            'link',
            'versions',
        ]);
    }

    public function test_modpack_build()
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $response = $this->get('api/modpack/'.$modpack->slug.'/'.$build->version);
        $response->assertOk();
        $response->assertJsonStructure([
            'minecraft',
            'forge',
            'java',
            'memory',
            'mods',
        ]);
    }

    public function test_mod_version()
    {
        $mod = Mod::find(1);
        $modversion = $mod->versions->first();
        $response = $this->get('api/mod/'.$mod->name.'/'.$modversion->version);
        $response->assertOk();
        $response->assertJsonStructure([
            'md5',
            'filesize',
            'url',
        ]);
    }

    public function test_modversion_with_invalid_mod()
    {
        $response = $this->get('api/mod/foo/bar');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);
    }

    public function test_invalid_modversion()
    {
        $mod = Mod::find(1);
        $response = $this->get('api/mod/'.$mod->name.'/invalid');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod version does not exist']);
    }
}
