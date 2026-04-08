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
        }
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
