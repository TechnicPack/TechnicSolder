<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase {

    use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();

        $this->seed();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testUserIndexGet()
	{
		$response = $this->get('/user');

		$response->assertRedirect('/user/list');
	}

	public function testUserListGet()
	{
		$response = $this->get('/user/list');

		$response->assertOk();
	}

	public function testUserCreateGet()
	{
		$response = $this->get('/user/create');

		$response->assertOk();
	}

	public function testUserCreatePostNonUniqueEmail()
	{
		$data = [
			'email' => 'admin@admin.com',
			'username' => 'test',
			'password' => 'B3sTp@ss'
		];

		$response = $this->post('/user/create', $data);
		$response->assertRedirect('/user/create');
		$response->assertSessionHasErrors('email');
	}

	public function testUserCreatePostNonUniqueUsername()
	{
		$data = [
			'email' => 'test@test.com',
			'username' => 'admin',
			'password' => 'B3sTp@ss'
		];

		$response = $this->post('/user/create', $data);
		$response->assertRedirect('/user/create');
		$response->assertSessionHasErrors('username');
	}

	public function testUserCreatePost() 
	{
		$data = [
			'email' => 'test@test.com',
			'username' => 'test',
			'password' => 'B3sTp@ss'
        ];

		$response = $this->post('/user/create', $data);
		$response->assertRedirect('/user/edit/2');
		$response->assertSessionHas('success');
	}

	public function testUserEditGet()
	{
		$user = User::where('username', 'test')->firstOrFail();
		
		$response = $this->get('/user/edit/'. $user->id);

		$response->assertOk();
	}

	public function testUserEditPostNonUniqueEmail()
	{
		$user = User::where('username', 'test')->firstOrFail();

		$data = [
			'email' => 'admin@admin.com',
			'username' => 'test'
        ];

		$response = $this->post('/user/edit/' . $user->id, $data);
		$response->assertRedirect('/user/edit/' . $user->id);
		$response->assertSessionHasErrors('email');
	}

	public function testUserEditPostNonUniqueUsername()
	{
		$user = User::where('username', 'test')->firstOrFail();

		$data = [
			'email' => 'test@test.com',
			'username' => 'admin'
        ];

		$response = $this->post('/user/edit/' . $user->id, $data);
		$response->assertRedirect('/user/edit/' . $user->id);
		$response->assertSessionHasErrors('username');
	}

	public function testUserEditPost() 
	{
		$user = User::where('username', 'test')->firstOrFail();

		$data = [
			'email' => 'test@test.com',
			'username' => 'test'
        ];

		$response = $this->post('/user/edit/' . $user->id, $data);
		$response->assertRedirect('/user/list');
		$response->assertSessionHas('success');
	}

	public function testUserDeleteGet()
	{
		$user = User::where('username', 'test')->firstOrFail();

		$response = $this->get('/user/delete/'.$user->id);

		$response->assertOk();
	}

	public function testUserDeleteGetInvalidID()
	{
		$response = $this->get('/user/delete/100000');
		$response->assertRedirect('/user/list');
	}

	public function testUserDeletePostInvalidID()
	{
		$response = $this->post('/user/delete/100000');
		$response->assertRedirect('/user/list');
	}

	public function testUserDeletePost()
	{
		$user = User::where('username', 'test')->firstOrFail();

		$response = $this->post('/user/delete/'.$user->id);
		$response->assertRedirect('/user/list');
		$response->assertSessionHas('success');
	}

	public function testUserCanNotDeleteLastAdminPost()
	{
		$response = $this->post('/user/delete/1');
		$response->assertRedirect('/user/list');
		$response->assertSessionHasErrors();
	}

	public function testUserCreateMoreSuperAdminsPost(){
		$data = [
			'email' => 'test-sadmin@test.com',
			'username' => 'sadmin',
			'password' => 'B3sT3Re4p@ss',
			'solder-full' => '1'
        ];

		$response = $this->post('/user/create', $data);
		$response->assertRedirect('/user/edit/3');
		$response->assertSessionHas('success');
	}

	public function testUserWarnDeleteSelfGet()
	{
		$crawler = $this->client->request('GET', '/user/delete/1');
		$delete_warning = $crawler->filter('h3 > i:contains("That\'s you!")');
		$this->assertCount(1, $delete_warning);
	}

	public function testUserDeleteFirstPost()
	{
		$response = $this->post('/user/delete/1');
		$response->assertRedirect('/user/list');
        $response->assertSessionHas('success');
	}
}