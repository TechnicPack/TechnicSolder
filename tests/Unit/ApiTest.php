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

    public function test_base(): void
    {
        $response = $this->get('api/');
        $response->assertOk();
        $response->assertJson([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM,
        ]);
    }

    public function test_modpack(): void
    {
        $response = $this->get('api/modpack');
        $response->assertOk();
        $response->assertJsonStructure(['modpacks', 'mirror_url']);
    }

    public function test_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod');
        $response->assertOk();
        $response->assertJsonStructure(['mods']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_invalid_modpack(): void
    {
        $response = $this->get('api/modpack/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_modpack_slug(): void
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

    public function test_invalid_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_mod_slug(): void
    {
        $mod = Mod::find(1);

        config()->set('solder.disable_mod_api', false);
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

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_modpack_build(): void
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

    public function test_mod_version(): void
    {
        $mod = Mod::find(1);
        $modversion = $mod->versions->first();

        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/'.$mod->name.'/'.$modversion->version);
        $response->assertOk();
        $response->assertJson([
            'md5' => $modversion->md5,
            'filesize' => $modversion->filesize,
            'url' => $modversion->url,
        ]);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name.'/'.$modversion->version);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_modversion_with_invalid_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/foo/bar');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/foo/bar');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_invalid_modversion(): void
    {
        $mod = Mod::find(1);

        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/'.$mod->name.'/invalid');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod version does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name.'/invalid');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }
}
