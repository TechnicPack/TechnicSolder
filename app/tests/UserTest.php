<?php

class UserTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Route::enableFilters();
		
		$user = User::find(1);
		$this->be($user);
	}

	public function testUserIndexGet()
	{
		$this->call('GET', '/user');

		$this->assertRedirectedTo('/user/list');
	}

	public function testUserListGet()
	{
		$this->call('GET', '/user/list');

		$this->assertResponseOk();
	}

	public function testUserCreateGet()
	{
		$this->call('GET', '/user/create');

		$this->assertResponseOk();
	}

	public function testUserCreatePostNonUniqueEmail()
	{
		$data = array(
			'email' => 'admin@admin.com',
			'username' => 'test',
			'password' => 'B3sTp@ss'
		);

		$response = $this->call('POST', '/user/create', $data);
		$this->assertRedirectedTo('/user/create');
		$this->assertSessionHasErrors('email');
	}

	public function testUserCreatePostNonUniqueUsername()
	{
		$data = array(
			'email' => 'test@test.com',
			'username' => 'admin',
			'password' => 'B3sTp@ss'
		);

		$response = $this->call('POST', '/user/create', $data);
		$this->assertRedirectedTo('/user/create');
		$this->assertSessionHasErrors('username');
	}

	public function testUserCreatePost() 
	{
		$data = array(
			'email' => 'test@test.com',
			'username' => 'test',
			'password' => 'B3sTp@ss'
		);

		$response = $this->call('POST', '/user/create', $data);
		$this->assertRedirectedTo('/user/edit/2');
		$this->assertSessionHas('success');
	}

	public function testUserEditGet()
	{
		$user = User::where('username', '=', 'test')->firstOrFail();
		
		$this->call('GET', '/user/edit/'. $user->id);

		$this->assertResponseOk();
	}

	public function testUserEditPostNonUniqueEmail()
	{
		$user = User::where('username', '=', 'test')->firstOrFail();

		$data = array(
			'email' => 'admin@admin.com',
			'username' => 'test'
		);

		$response = $this->call('POST', '/user/edit/' . $user->id, $data);
		$this->assertRedirectedTo('/user/edit/' . $user->id);
		$this->assertSessionHasErrors('email');
	}

	public function testUserEditPostNonUniqueUsername()
	{
		$user = User::where('username', '=', 'test')->firstOrFail();

		$data = array(
			'email' => 'test@test.com',
			'username' => 'admin'
		);

		$response = $this->call('POST', '/user/edit/' . $user->id, $data);
		$this->assertRedirectedTo('/user/edit/' . $user->id);
		$this->assertSessionHasErrors('username');
	}

	public function testUserEditPost() 
	{
		$user = User::where('username', '=', 'test')->firstOrFail();

		$data = array(
			'email' => 'test@test.com',
			'username' => 'test'
		);

		$response = $this->call('POST', '/user/edit/' . $user->id, $data);
		$this->assertRedirectedTo('/user/list');
		$this->assertSessionHas('success');
	}

	public function testUserDeleteGet()
	{
		$user = User::where('username', '=', 'test')->firstOrFail();

		$this->call('GET', '/user/delete/'.$user->id);

		$this->assertResponseOk();
	}

	public function testUserDeleteGetInvalidID()
	{
		$this->call('GET', '/user/delete/100000');
		$this->assertRedirectedTo('/user/list');
	}

	public function testUserDeletePostInvalidID()
	{
		$this->call('POST', '/user/delete/100000');
		$this->assertRedirectedTo('/user/list');
	}

	public function testUserDeletePost()
	{
		$user = User::where('username', '=', 'test')->firstOrFail();

		$this->call('POST', '/user/delete/'.$user->id);
		$this->assertRedirectedTo('/user/list');
		$this->assertSessionHas('success');
	}

	public function testUserCanNotDeleteLastAdminPost()
	{
		$this->call('POST', '/user/delete/1');
		$this->assertRedirectedTo('/user/list');
		$this->assertSessionHasErrors();
	}

	public function testUserCreateMoreSuperAdminsPost(){
		$data = array(
			'email' => 'test-sadmin@test.com',
			'username' => 'sadmin',
			'password' => 'B3sT3Re4p@ss',
			'solder-full' => '1'
		);

		$response = $this->call('POST', '/user/create', $data);
		$this->assertRedirectedTo('/user/edit/3');
		$this->assertSessionHas('success');
	}

	public function testUserWarnDeleteSelfGet()
	{
		$crawler = $this->client->request('GET', '/user/delete/1');
		$delete_warning = $crawler->filter('h3 > i:contains("That\'s you!")');
		$this->assertCount(1, $delete_warning);
	}

	public function testUserDeleteFirstPost()
	{
		$this->call('POST', '/user/delete/1');
		$this->assertRedirectedTo('/user/list');
		$this->assertSessionHas('success');
	}
}