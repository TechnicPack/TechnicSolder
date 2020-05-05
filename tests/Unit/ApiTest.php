<?php

namespace Tests\Unit;

use App\Mod;
use App\Modpack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testBase()
    {
        $response = $this->get('api/');
        $response->assertOk();
        $response->assertJson([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM,
        ]);
    }

    public function testModpack()
    {
        $response = $this->get('api/modpack');
        $response->assertOk();
        $response->assertJsonStructure(['modpacks', 'mirror_url']);
    }

    public function testMod()
    {
        $response = $this->get('api/mod');
        $response->assertOk();
        $response->assertJsonStructure(['mods']);
    }

    public function testInvalidModpack()
    {
        $response = $this->get('api/modpack/bob');
        $response->assertOk();
        $response->assertJsonStructure(['error']);
    }

    public function testModpackSlug()
    {
        $modpack = Modpack::find(1);
        $response = $this->get('api/modpack/' . $modpack->slug);
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

    public function testInvalidMod()
    {
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJsonStructure(['error']);
    }

    public function testModSlug()
    {
        $mod = Mod::find(1);
        $response = $this->get('api/mod/' . $mod->name);
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

    public function testModpackBuild()
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $response = $this->get('api/modpack/' . $modpack->slug . '/' . $build->version);
        $response->assertOk();
        $response->assertJsonStructure([
            'minecraft',
            'forge',
            'java',
            'memory',
            'mods',
        ]);
    }

    public function testModVersion()
    {
        $mod = Mod::find(1);
        $modversion = $mod->versions->first();
        $response = $this->get('api/mod/' . $mod->name . '/' . $modversion->version);
        $response->assertOk();
        $response->assertJsonStructure([
            'md5',
            'filesize',
            'url',
        ]);
    }
}
