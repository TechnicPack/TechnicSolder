<?php

namespace Tests\Unit;

use App\Key;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeyTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function testKeyIndex()
    {
        $response = $this->get('/key');

        $response->assertRedirect('/key/list');
    }

    public function testKeyCreateGet()
    {
        $response = $this->get('/key/create');

        $response->assertOk();
    }

    public function testKeyCreatePostNonUniqueKey()
    {
        $data = [
            'name' => 'TestKey2',
            'api_key' => 'sfIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
        ];

        $response = $this->post('/key/create', $data);
        $response->assertRedirect('/key/create');
        $response->assertSessionHasErrors('api_key');
    }

    public function testKeyCreatePostNonUniqueName()
    {
        $data = [
            'name' => 'TestKey',
            'api_key' => 'abIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
        ];

        $response = $this->post('/key/create', $data);
        $response->assertRedirect('/key/create');
        $response->assertSessionHasErrors('name');
    }

    public function testKeyCreatePost()
    {
        $data = [
            'name' => 'TestKey2',
            'api_key' => 'abCvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ'
        ];

        $response = $this->post('/key/create', $data);
        $response->assertRedirect('/key/list');
        $response->assertSessionHas('success');
    }

    public function testKeyDeleteGet()
    {
        $key = Key::find(1);

        $response = $this->get('/key/delete/' . $key->id);

        $response->assertOk();
    }

    public function testKeyDeleteGetInvalidID()
    {
        $response = $this->get('/key/delete/100000');
        $response->assertRedirect('/key/list');
    }

    public function testKeyDeletePostInvalidID()
    {
        $response = $this->post('/key/delete/100000');
        $response->assertRedirect('/key/list');
    }

    public function testKeyDeletePost()
    {
        $key = Key::firstOrFail();

        $response = $this->post('/key/delete/' . $key->id);
        $response->assertRedirect('/key/list');
        $response->assertSessionHas('success');
    }

    public function testKeyList()
    {
        $response = $this->get('/key/list');

        $response->assertOk();
    }
}
