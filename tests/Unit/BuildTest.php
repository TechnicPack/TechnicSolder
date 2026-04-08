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

    public function test_build_export_csv(): void
    {
        $response = $this->get('/modpack/build/1/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=testmodpack_1.0.0.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('mod_name,mod_slug,version,md5,filesize', $content);
        $this->assertStringContainsString('TestMod,testmod,1.0,bdbc6c6cc48c7b037e4aef64b58258a3,295', $content);
    }

    public function test_build_export_csv_invalid_build(): void
    {
        $response = $this->get('/modpack/build/999/export');

        $response->assertRedirect('modpack/list');
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

    public function test_build_create_clone_from_same_modpack(): void
    {
        $modpack = Modpack::first();
        $sourceBuild = $modpack->builds()->first();

        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'clone' => $sourceBuild->id,
        ];

        $response = $this->post('/modpack/add-build/'.$modpack->id, $data);

        $newBuild = Build::where('version', '1.1.0')->where('modpack_id', $modpack->id)->first();
        $this->assertNotNull($newBuild);

        $response->assertRedirect('/modpack/build/'.$newBuild->id);
        $this->assertEquals($sourceBuild->modversions->count(), $newBuild->modversions->count());
    }

    public function test_build_create_clone_from_different_modpack(): void
    {
        $sourceModpack = Modpack::first();
        $sourceBuild = $sourceModpack->builds()->first();

        $targetModpack = Modpack::create([
            'name' => 'TargetPack',
            'slug' => 'targetpack',
            'icon_url' => URL::asset('/resources/default/icon.png'),
            'logo_url' => URL::asset('/resources/default/logo.png'),
            'background_url' => URL::asset('/resources/default/background.jpg'),
        ]);

        $data = [
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'clone' => $sourceBuild->id,
        ];

        $response = $this->post('/modpack/add-build/'.$targetModpack->id, $data);

        $newBuild = Build::where('version', '1.0.0')->where('modpack_id', $targetModpack->id)->first();
        $this->assertNotNull($newBuild);

        $response->assertRedirect('/modpack/build/'.$newBuild->id);
        $this->assertEquals($sourceBuild->modversions->count(), $newBuild->modversions->count());
    }

    public function test_build_create_clone_source_null_check(): void
    {
        $modpack = Modpack::first();

        $data = [
            'version' => '1.1.0',
            'minecraft' => '1.7.10',
            'clone' => 99999,
        ];

        $response = $this->post('/modpack/add-build/'.$modpack->id, $data);

        $newBuild = Build::where('version', '1.1.0')->where('modpack_id', $modpack->id)->first();
        $this->assertNotNull($newBuild);

        $response->assertRedirect('/modpack/build/'.$newBuild->id);
        $response->assertSessionHasErrors();
    }

    public function test_build_create_clone_from_inaccessible_modpack(): void
    {
        $sourceModpack = Modpack::first();
        $sourceBuild = $sourceModpack->builds()->first();

        $restrictedUser = User::create([
            'username' => 'restricted',
            'email' => 'restricted@test.com',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
        ]);

        $targetModpack = Modpack::create([
            'name' => 'RestrictedPack',
            'slug' => 'restrictedpack',
            'icon_url' => URL::asset('/resources/default/icon.png'),
            'logo_url' => URL::asset('/resources/default/logo.png'),
            'background_url' => URL::asset('/resources/default/background.jpg'),
        ]);

        $restrictedUser->permission()->create([
            'solder_full' => false,
            'modpacks_create' => true,
            'modpacks_manage' => true,
            'modpacks' => [$targetModpack->id],
        ]);

        $this->actingAs($restrictedUser);

        $data = [
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'clone' => $sourceBuild->id,
        ];

        $response = $this->post('/modpack/add-build/'.$targetModpack->id, $data);

        $newBuild = Build::where('version', '1.0.0')->where('modpack_id', $targetModpack->id)->first();
        $this->assertNotNull($newBuild);
        $response->assertRedirect('/modpack/build/'.$newBuild->id);
        $response->assertSessionHasErrors();
    }
}
