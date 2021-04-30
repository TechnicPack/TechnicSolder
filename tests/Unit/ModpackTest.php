<?php

namespace Tests\Unit;

use App\Modpack;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModpackTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function testModpackIndex()
    {
        $response = $this->get('/modpack');

        $response->assertRedirect('/modpack/list');
    }

    public function testModpackList()
    {
        $response = $this->get('/modpack/list');

        $response->assertOk();
    }

    public function testModpackCreateGet()
    {
        $response = $this->get('/modpack/create');

        $response->assertOk();
    }

    public function testModpackCreatePostNonUniqueSlug()
    {
        $data = [
            'name' => 'TestModpack2',
            'slug' => 'testmodpack'
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/create');
        $response->assertSessionHasErrors('slug');
    }

    public function testModpackCreatePostNonUniqueName()
    {
        $data = [
            'name' => 'TestModpack',
            'slug' => 'testmodpack2'
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/create');
        $response->assertSessionHasErrors('name');
    }

    public function testModpackCreatePost()
    {
        $data = [
            'name' => 'TestModpack2',
            'slug' => 'testmodpack2'
        ];

        $response = $this->post('/modpack/create', $data);
        $response->assertRedirect('/modpack/view/2');
    }

    public function testModpackDeleteGet()
    {
        $modpack = Modpack::find(1);

        $response = $this->get('/modpack/delete/' . $modpack->id);

        $response->assertOk();
    }

    public function testModpackDeleteGetInvalidID()
    {
        $response = $this->get('/modpack/delete/100000');
        $response->assertRedirect('/modpack/list');
    }

    public function testModpackDeletePostInvalidID()
    {
        $response = $this->post('/modpack/delete/100000');
        $response->assertRedirect('/modpack/list');
    }

    public function testModpackDeletePost()
    {
        $modpack = Modpack::firstOrFail();

        $response = $this->post('/modpack/delete/' . $modpack->id);
        $response->assertRedirect('/modpack/list');
        $response->assertSessionHas('success');
    }

    public function testModpackBuild()
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds()->first();

        $response = $this->get('/modpack/build/' . $build->id);

        $response->assertOk();
    }

    public function testModpackEditGet()
    {
        $modpack = Modpack::find(1);

        $response = $this->get('/modpack/edit/' . $modpack->id);

        $response->assertOk();
    }

    public function testModpackEditPostBlank()
    {
        $modpack = Modpack::find(1);

        $data = [
            'name' => $modpack->name,
            'slug' => $modpack->slug,
            'hidden' => $modpack->hidden,
            'private' => $modpack->private
        ];

        $response = $this->post('/modpack/edit/' . $modpack->id, $data);
        $response->assertRedirect('/modpack/view/' . $modpack->id);
    }

    public function testModpackEditPost()
    {
        $modpack = Modpack::find(1);

        $data = [
            'name' => 'TestTest',
            'slug' => 'test-test',
            'hidden' => true,
            'private' => true
        ];

        $response = $this->post('/modpack/edit/' . $modpack->id, $data);
        $response->assertRedirect('/modpack/view/' . $modpack->id);
        $modpack = Modpack::find(1);
        $this->assertEquals('TestTest', $modpack->name);
        $this->assertEquals('test-test', $modpack->slug);
        $this->assertTrue((bool) ($modpack->hidden));
        $this->assertTrue((bool) ($modpack->private));
    }

}
