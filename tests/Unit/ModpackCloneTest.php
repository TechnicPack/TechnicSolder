<?php

namespace Tests\Unit;

use App\Models\Build;
use App\Models\Modpack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ModpackCloneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_clone_modpack_get(): void
    {
        $modpack = Modpack::first();

        $response = $this->get('/modpack/clone/'.$modpack->id);

        $response->assertOk();
    }

    public function test_clone_modpack_post(): void
    {
        $modpack = Modpack::first();

        $data = [
            'name' => 'Cloned Modpack',
            'slug' => 'cloned-modpack',
        ];

        $response = $this->post('/modpack/clone/'.$modpack->id, $data);

        $newModpack = Modpack::where('slug', 'cloned-modpack')->first();
        $this->assertNotNull($newModpack);
        $response->assertRedirect('/modpack/view/'.$newModpack->id);
    }

    public function test_clone_modpack_copies_builds(): void
    {
        $modpack = Modpack::with('builds.modversions')->first();
        $modpack->builds->first()->update([
            'min_java' => '1.8',
            'java_runtime' => 'java-runtime-delta',
        ]);
        $originalBuildCount = $modpack->builds->count();

        $data = [
            'name' => 'Cloned With Builds',
            'slug' => 'cloned-with-builds',
        ];

        $this->post('/modpack/clone/'.$modpack->id, $data);

        $newModpack = Modpack::where('slug', 'cloned-with-builds')->first();
        $this->assertNotNull($newModpack);
        $this->assertEquals($originalBuildCount, $newModpack->builds()->count());

        // Verify modversion assignments were copied
        foreach ($modpack->builds as $originalBuild) {
            $clonedBuild = Build::where('modpack_id', $newModpack->id)
                ->where('version', $originalBuild->version)
                ->first();
            $this->assertNotNull($clonedBuild);
            $this->assertEquals(
                $originalBuild->modversions()->count(),
                $clonedBuild->modversions()->count()
            );
            $this->getJson('api/modpack/'.$newModpack->slug.'/'.$clonedBuild->version)
                ->assertOk()
                ->assertJson([
                    'java' => $originalBuild->min_java,
                    'java_runtime' => $originalBuild->java_runtime,
                ]);
        }
    }

    public function test_api_clone_modpack_preserves_launcher_runtime_and_mods(): void
    {
        $modpack = Modpack::with('builds')->first();
        $build = $modpack->builds->first();
        $build->update([
            'min_java' => '1.8',
            'java_runtime' => 'java-runtime-delta',
        ]);
        $source = $this->getJson('api/modpack/'.$modpack->slug.'/'.$build->version)
            ->assertOk();
        $token = User::find(1)->createToken('clone-test')->plainTextToken;

        $this->postJson('api/modpack/'.$modpack->slug.'/clone', [
            'name' => 'API Cloned Pack',
            'slug' => 'api-cloned-pack',
        ], ['Authorization' => 'Bearer '.$token])->assertStatus(201);

        $this->getJson('api/modpack/api-cloned-pack/'.$build->version)
            ->assertOk()
            ->assertJsonPath('java', '1.8')
            ->assertJsonPath('java_runtime', 'java-runtime-delta')
            ->assertJsonPath('mods', $source->json('mods'));
    }

    public function test_clone_modpack_duplicate_slug_fails(): void
    {
        $modpack = Modpack::first();

        $data = [
            'name' => 'Another Modpack',
            'slug' => $modpack->slug,
        ];

        $response = $this->post('/modpack/clone/'.$modpack->id, $data);
        $response->assertRedirect('/modpack/clone/'.$modpack->id);
        $response->assertSessionHasErrors('slug');
    }
}
