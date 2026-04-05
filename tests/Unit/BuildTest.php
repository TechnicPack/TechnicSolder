<?php

namespace Tests\Unit;

use App\Models\Build;
use App\Models\Modpack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BuildTest extends TestCase
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

        $response = $this->get('/modpack/build/'.$build->id.'/edit');
        $response->assertOk();
    }

    public function test_build_edit_post(): void
    {
        $build = Build::find(1);

        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'/edit', $data);
        $response->assertRedirect('/modpack/build/'.$build->id);

        $build = Build::find(1);

        $this->assertEquals($build->min_memory, '1024');
        $this->assertEquals($build->min_java, '1.8');
    }

    public function test_build_delete_get(): void
    {
        $build = Build::find(1);

        $response = $this->get('/modpack/build/'.$build->id.'/delete');
        $response->assertOk();
    }

    public function test_build_delete_post(): void
    {
        $build = Build::find(1);

        $response = $this->post('/modpack/build/'.$build->id.'/delete');
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
            'version' => '2.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'/edit', $data);
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
            'version' => '2.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '1.8',
            'memory' => '1024',
            'memory-enabled' => '1',
        ];

        $response = $this->post('/modpack/build/'.$build->id.'/edit', $data);
        $response->assertRedirect('/modpack/build/'.$build->id);

        // Refresh the models
        $modpack = Modpack::first();

        $build = $modpack->builds()->first();

        // Sanity check
        $this->assertEquals($data['version'], $build->version);

        // Check the latest version changed along with the version
        $this->assertEquals($build->version, $modpack->latest);
    }

    public function test_clone_build_cannot_cross_modpack_boundary(): void
    {
        // Create a second modpack with a build that has the seeded modversion
        $otherModpack = Modpack::create([
            'name' => 'PrivateModpack',
            'slug' => 'privatemodpack',
            'hidden' => false,
            'private' => true,
        ]);

        $otherBuild = Build::create([
            'modpack_id' => $otherModpack->id,
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'is_published' => true,
        ]);

        // Attach the seeded modversion (id=1) to the other build
        $otherBuild->modversions()->attach(1);

        // Attempt to clone the other modpack's build into modpack 1
        $data = [
            'version' => '2.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '',
            'memory-enabled' => 0,
            'clone' => $otherBuild->id,
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect();

        // The new build should exist but have NO modversions (clone was cross-modpack)
        $newBuild = Build::where('version', '2.0.0')->where('modpack_id', 1)->first();
        $this->assertNotNull($newBuild);
        $this->assertCount(0, $newBuild->modversions);
    }

    public function test_clone_build_within_same_modpack(): void
    {
        // Build 1 (from seeder) belongs to modpack 1 and has modversion 1 attached
        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'java-version' => '',
            'memory-enabled' => 0,
            'clone' => 1, // Clone from build 1, same modpack
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect();

        $newBuild = Build::where('version', '1.1.0')->where('modpack_id', 1)->first();
        $this->assertNotNull($newBuild);
        $this->assertCount(1, $newBuild->modversions);
    }

    public function test_clone_nonexistent_build_id_is_ignored(): void
    {
        $data = [
            'version' => '3.0.0',
            'minecraft' => '1.7.10',
            'java-version' => '',
            'memory-enabled' => 0,
            'clone' => 9999, // Does not exist
        ];

        $response = $this->post('/modpack/add-build/1', $data);
        $response->assertRedirect();

        $newBuild = Build::where('version', '3.0.0')->where('modpack_id', 1)->first();
        $this->assertNotNull($newBuild);
        $this->assertCount(0, $newBuild->modversions);
    }
}
