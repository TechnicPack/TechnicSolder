<?php

namespace Tests\Unit;

use App\Models\Modpack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ModpackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_modpack_index(): void
    {
        $response = $this->get('/modpack');

        $response->assertRedirect('/modpack/list');
    }

    public function test_modpack_list(): void
    {
        $response = $this->get('/modpack/list');

        $response->assertOk();
    }

    public function test_modpack_create_get(): void
    {
        $response = $this->get('/modpack/create');

        $response->assertOk();
    }

    public function test_modpack_create_post_duplicate_slug(): void
    {
        $data = [
            'name' => 'TestModpack2',
            'slug' => 'testmodpack',
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/create');
        $response->assertSessionHasErrors('slug');
    }

    public function test_modpack_create_post_duplicate_name(): void
    {
        $data = [
            'name' => 'TestModpack',
            'slug' => 'testmodpack2',
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/create');
        $response->assertSessionHasErrors('name');
    }

    public function test_modpack_create_post(): void
    {
        $data = [
            'name' => 'TestModpack2',
            'slug' => 'testmodpack2',
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/view/2');
    }

    public function test_modpack_delete_get(): void
    {
        $modpack = Modpack::find(1);

        $response = $this->get('/modpack/delete/'.$modpack->id);

        $response->assertOk();
    }

    public function test_modpack_delete_get_invalid_id(): void
    {
        $response = $this->get('/modpack/delete/100000');
        $response->assertRedirect('/modpack/list');
    }

    public function test_modpack_delete_post_invalid_id(): void
    {
        $response = $this->post('/modpack/delete/100000');
        $response->assertRedirect('/modpack/list');
    }

    public function test_modpack_delete_post(): void
    {
        $modpack = Modpack::firstOrFail();

        $response = $this->post('/modpack/delete/'.$modpack->id);
        $response->assertRedirect('/modpack/list');
        $response->assertSessionHas('success');
    }

    public function test_modpack_build(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds()->first();

        $response = $this->get('/modpack/build/'.$build->id);

        $response->assertOk();
    }

    public function test_modpack_edit_get(): void
    {
        $modpack = Modpack::find(1);

        $response = $this->get('/modpack/edit/'.$modpack->id);

        $response->assertOk();
    }

    public function test_modpack_edit_post_blank(): void
    {
        $modpack = Modpack::find(1);

        $data = [
            'name' => $modpack->name,
            'slug' => $modpack->slug,
            'hidden' => $modpack->hidden,
            'private' => $modpack->private,
        ];

        $response = $this->post('/modpack/edit/'.$modpack->id, $data);
        $response->assertRedirect('/modpack/view/'.$modpack->id);
    }

    public function test_modpack_edit_post(): void
    {
        $modpack = Modpack::find(1);

        $data = [
            'name' => 'TestTest',
            'slug' => 'test-test',
            'hidden' => true,
            'private' => true,
        ];

        $response = $this->post('/modpack/edit/'.$modpack->id, $data);
        $response->assertRedirect('/modpack/view/'.$modpack->id);
        $modpack = Modpack::find(1);
        $this->assertEquals('TestTest', $modpack->name);
        $this->assertEquals('test-test', $modpack->slug);
        $this->assertTrue((bool) ($modpack->hidden));
        $this->assertTrue((bool) ($modpack->private));
    }
}
