<?php

namespace Tests\Unit;

use App\Models\Build;
use App\Models\Modpack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_build_get(): void
    {
        $response = $this->get('/modpack/build/1');

        $response->assertOk();
    }

    public function test_build_add_get(): void
    {
        $response = $this->get('/modpack/add-build/1');

        $response->assertOk();
    }

    public function test_build_add_post(): void
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '1536');
        $this->assertEquals($build->min_java, '1.7');
    }

    public function test_build_add_post_empty_version(): void
    {
        $data = [
            'version' => '',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/add-build/1');
        $response->assertSessionHasErrors('version');
    }

    public function test_build_add_post_empty_minecraft(): void
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '',
            'java-version' => '1.7',
            'memory' => '1536',
            'memory-enabled' => 1,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/add-build/1');
        $response->assertSessionHasErrors('minecraft');
    }

    public function test_build_add_post_empty_java(): void
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '',
            'memory' => '1536',
            'memory-enabled' => 1,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '1536');
        $this->assertEquals($build->min_java, '');
    }

    public function test_build_add_post_no_memory(): void
    {
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.7',
            'memory' => 0,
            'memory-enabled' => 0,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect('/modpack/build/2');

        $build = Build::find(2);

        $this->assertEquals($build->version, '1.1.0');
        $this->assertEquals($build->minecraft, '1.7.10');
        $this->assertEquals($build->min_memory, '0');
        $this->assertEquals($build->min_java, '1.7');
    }

    public function test_build_edit_get(): void
    {
        $build = Build::find(1);

        $response = $this->get('/modpack/build/'.$build->id.'?action=edit');
        $response->assertOk();
    }

    public function test_build_edit_post(): void
    {
        $build = Build::find(1);

        $data = [
            'confirm-edit' => '1',
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'?action=edit', $data);
        $response->assertRedirect('/modpack/build/'.$build->id);

        $build = Build::find(1);

        $this->assertEquals($build->min_memory, '1024');
        $this->assertEquals($build->min_java, '1.8');
    }

    public function test_build_delete_get(): void
    {
        $build = Build::find(1);

        $response = $this->get('/modpack/build/'.$build->id.'?action=delete');
        $response->assertOk();
    }

    public function test_build_delete_post(): void
    {
        $build = Build::find(1);

        $data = [
            'confirm-delete' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'?action=delete', $data);
        $response->assertRedirect('modpack/view/'.$build->modpack->id);
    }

    public function test_renaming_recommended_build_changes_build_in_modpack(): void
    {
        $modpack = Modpack::first();

        $build = $modpack->builds()->first();

        // Set it as recommended
        $modpack->recommended = $build->version;
        $modpack->save();

        // Sanity check
        $this->assertEquals($build->version, $modpack->recommended);

        // Edit the build
        $data = [
            'confirm-edit' => '1',
            'version' => '2.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'?action=edit', $data);
        $response->assertRedirect('/modpack/build/'.$build->id);

        // Refresh the models
        $modpack = Modpack::first();

        $build = $modpack->builds()->first();

        // Sanity check
        $this->assertEquals($data['version'], $build->version);

        // Check the recommended version changed along with the version
        $this->assertEquals($build->version, $modpack->recommended);
    }

    public function test_renaming_latest_build_changes_build_in_modpack(): void
    {
        $modpack = Modpack::first();

        $build = $modpack->builds()->first();

        // Set it as latest
        $modpack->latest = $build->version;
        $modpack->save();

        // Sanity check
        $this->assertEquals($build->version, $modpack->latest);

        // Edit the build
        $data = [
            'confirm-edit' => '1',
            'version' => '2.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'?action=edit', $data);
        $response->assertRedirect('/modpack/build/'.$build->id);

        // Refresh the models
        $modpack = Modpack::first();

        $build = $modpack->builds()->first();

        // Sanity check
        $this->assertEquals($data['version'], $build->version);

        // Check the latest version changed along with the version
        $this->assertEquals($build->version, $modpack->latest);
    }
}
