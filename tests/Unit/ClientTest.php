<?php

namespace Tests\Unit;

use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function testClientIndex()
    {
        $response = $this->get('/client');

        $response->assertRedirect('/client/list');
    }

    public function testClientCreateGet()
    {
        $response = $this->get('/client/create');

        $response->assertOk();
    }

    public function testClientCreatePostUnUniqueUUID()
    {
        $data = [
            'name' => 'TestClient2',
            'uuid' => '2ezf6f26-eb15-4ccb-9f0b-8z5ed2c72946'
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/create');
        $response->assertSessionHasErrors('uuid');
    }

    public function testClientCreatePostUnUniqueName()
    {
        $data = [
            'name' => 'TestClient',
            'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946'
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/create');
        $response->assertSessionHasErrors('name');
    }

    public function testClientCreatePost()
    {
        $data = [
            'name' => 'TestClient2',
            'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946'
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/list');
        $response->assertSessionHas('success');
    }

    public function testClientDeleteGet()
    {
        $client = Client::find(1);

        $response = $this->get('/client/delete/' . $client->id);

        $response->assertOk();
    }

    public function testClientDeleteGetInvalidID()
    {
        $response = $this->get('/client/delete/100000');
        $response->assertRedirect('/client/list');
    }

    public function testClientDeletePostInvalidID()
    {
        $response = $this->post('/client/delete/100000');
        $response->assertRedirect('/client/list');
    }

    public function testClientDeletePost()
    {
        $client = Client::where('name', 'TestClient')->firstOrFail();

        $response = $this->post('/client/delete/' . $client->id);
        $response->assertRedirect('/client/list');
        $response->assertSessionHas('success');
    }

    public function testClientList()
    {
        $response = $this->get('/client/list');

        $response->assertOk();
    }
}
