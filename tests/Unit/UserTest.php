<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_user_index_get()
    {
        $response = $this->get('/user');

        $response->assertRedirect('/user/list');
    }

    public function test_user_list_get()
    {
        $response = $this->get('/user/list');

        $response->assertOk();
    }

    public function test_user_create_get()
    {
        $response = $this->get('/user/create');

        $response->assertOk();
    }

    public function test_user_create_post_duplicate_email()
    {
        $data = [
            'email' => 'admin@admin.com',
            'username' => 'test',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/create');
        $response->assertSessionHasErrors('email');
    }

    public function test_user_create_post_duplicate_username()
    {
        $data = [
            'email' => 'test@test.com',
            'username' => 'admin',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/create');
        $response->assertSessionHasErrors('username');
    }

    public function test_user_create_post()
    {
        $data = [
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/edit/2');
        $response->assertSessionHas('success');
    }

    public function test_user_edit_get()
    {
        $user = User::firstOrFail();

        $response = $this->get('/user/edit/'.$user->id);

        $response->assertOk();
    }

    public function test_user_edit_post_duplicate_email()
    {
        $user = User::firstOrFail();

        // Create second user
        User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        $data = [
            'email' => 'test@test.com',
            'username' => 'test',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('email');
    }

    public function test_user_edit_post_duplicate_username()
    {
        // Create second user
        $user = User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        $data = [
            'email' => 'test@test.com',
            'username' => 'admin',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('username');
    }

    public function test_user_edit_post()
    {
        $user = User::firstOrFail();

        $data = [
            'email' => 'admin2@admin.com',
            'username' => 'admin2',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/list');
        $response->assertSessionHas('success');
    }

    public function test_user_delete_get()
    {
        // Create second user
        $user = User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        $perm = new UserPermission();
        $perm->user_id = $user->id;
        $perm->save();

        $response = $this->get('/user/delete/'.$user->id);
        $response->assertOk();
    }

    public function test_user_delete_get_invalid_id()
    {
        $response = $this->get('/user/delete/100000');
        $response->assertRedirect('/user/list');
    }

    public function test_user_delete_post_invalid_id()
    {
        $response = $this->post('/user/delete/100000');
        $response->assertRedirect('/user/list');
    }

    public function test_user_delete_post()
    {
        // Create second user
        $user = User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        $perm = new UserPermission();
        $perm->user_id = $user->id;
        $perm->save();

        $response = $this->post('/user/delete/'.$user->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHas('success');
    }

    public function test_user_cannot_delete_last_admin_post()
    {
        // Create second user
        $user = User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        // Allow this user to manage users, but not be an admin
        $perm = new UserPermission();
        $perm->user_id = $user->id;
        $perm->solder_users = 1;
        $perm->save();

        // Auth as the new user
        $this->be($user);

        $response = $this->post('/user/delete/1');
        $response->assertRedirect('/user/list');
        $response->assertSessionHasErrors();
    }

    public function test_user_cannot_delete_self()
    {
        $response = $this->post('/user/delete/'.auth()->user()->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHasErrors();
    }

    public function test_user_create_more_superadmins_post()
    {
        $data = [
            'email' => 'test-sadmin@test.com',
            'username' => 'sadmin',
            'password' => 'B3sT3Re4p@ss',
            'solder-full' => '1',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/edit/2');
        $response->assertSessionHas('success');
    }

    public function test_user_delete_first_post()
    {
        // Create second user
        $user = User::create([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]);

        $perm = new UserPermission();
        $perm->user_id = $user->id;
        $perm->solder_full = 1;
        $perm->save();

        $this->assertEquals(2, $user->id);

        $this->be($user);

        $response = $this->post('/user/delete/1');
        $response->assertRedirect('/user/list');
        $response->assertSessionHas('success');
    }
}
