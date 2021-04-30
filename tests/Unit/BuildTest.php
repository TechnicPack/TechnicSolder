<?php

namespace Tests\Unit;

use App\Build;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function testBuildGet()
    {
        $response = $this->get('/modpack/build/1');

        $response->assertOk();
    }

    public function testBuildAddGet()
    {
        $response = $this->get('/modpack/add-build/1');

        $response->assertOk();
    }

    public function testBuildAddPost()
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '1536');
        $this->assertEquals($build->min_java, '1.7');
    }

    public function testBuildAddPostEmptyVersion()
    {
        $data = [
            'version' => '',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/add-build/1');
        $response->assertSessionHasErrors('version');
    }

    public function testBuildAddPostEmptyMinecraft()
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/add-build/1');
        $response->assertSessionHasErrors('minecraft');
    }

    public function testBuildAddPostEmptyJava()
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '',
            'memory' => '1536',
            'memory-enabled' => 1
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '1536');
        $this->assertEquals($build->min_java, '');
    }

    public function testBuildAddPostNoMemory()
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => 0,
            'memory-enabled' => 0
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '0');
        $this->assertEquals($build->min_java, '1.7');
    }

    public function testBuildEditGet()
    {
        $build = Build::find(1);

        $response = $this->get('/modpack/build/' . $build->id . '?action=edit');
        $response->assertOk();
    }

    public function testBuildEditPost()
    {
        $build = Build::find(1);

        $data = [
            'confirm-edit' => '1',
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1'
        ];

        $response = $this->post('/modpack/build/' . $build->id . '?action=edit', $data);
        $response->assertRedirect('/modpack/build/1');

        $build = Build::find(1);

        $this->assertEquals($build->min_memory, '1024');
        $this->assertEquals($build->min_java, '1.8');
    }


    public function testBuildDeleteGet()
    {
        $build = Build::find(1);

        $response = $this->get('/modpack/build/' . $build->id . '?action=delete');
        $response->assertOk();
    }

    public function testBuildDeletePost()
    {
        $build = Build::find(1);

        $data = [
            'confirm-delete' => '1'
        ];

        $response = $this->post('/modpack/build/' . $build->id . '?action=delete', $data);
        $response->assertRedirect('modpack/view/' . $build->modpack->id);
    }
}
