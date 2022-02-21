<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_client_index()
    {
        $response = $this->get('/client');

        $response->assertRedirect('/client/list');
    }

    public function test_client_create_get()
    {
        $response = $this->get('/client/create');

        $response->assertOk();
    }

    public function test_client_create_post_duplicate_uuid()
    {
        $data = [
            'name' => 'TestClient2',
            'uuid' => '2ezf6f26-eb15-4ccb-9f0b-8z5ed2c72946',
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/create');
        $response->assertSessionHasErrors('uuid');
    }

    public function test_client_create_post_duplicate_name()
    {
        $data = [
            'name' => 'TestClient',
            'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946',
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/create');
        $response->assertSessionHasErrors('name');
    }

    public function test_client_create_post()
    {
        $data = [
            'name' => 'TestClient2',
            'uuid' => '3abf6f26-eb15-4ccb-9f0b-8z5ed3c72946',
        ];

        $response = $this->post('/client/create', $data);
        $response->assertRedirect('/client/list');
        $response->assertSessionHas('success');
    }

    public function test_client_delete_get()
    {
        $client = Client::find(1);

        $response = $this->get('/client/delete/'.$client->id);

        $response->assertOk();
    }

    public function test_client_delete_get_invalid_id()
    {
        $response = $this->get('/client/delete/100000');
        $response->assertRedirect('/client/list');
    }

    public function test_client_delete_post_invalid_id()
    {
        $response = $this->post('/client/delete/100000');
        $response->assertRedirect('/client/list');
    }

    public function test_client_delete_post()
    {
        $client = Client::where('name', 'TestClient')->firstOrFail();

        $response = $this->post('/client/delete/'.$client->id);
        $response->assertRedirect('/client/list');
        $response->assertSessionHas('success');
    }

    public function test_client_list()
    {
        $response = $this->get('/client/list');

        $response->assertOk();
    }
}
