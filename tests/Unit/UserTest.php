<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_user_index_get(): void
    {
        $response = $this->get('/user');

        $response->assertRedirect('/user/list');
    }

    public function test_user_list_get(): void
    {
        $response = $this->get('/user/list');

        $response->assertOk();
    }

    public function test_user_create_get(): void
    {
        $response = $this->get('/user/create');

        $response->assertOk();
    }

    public function test_user_create_get_password_field_hints_new_password(): void
    {
        $response = $this->get('/user/create');

        $response->assertOk();
        $this->assertSame(
            1,
            substr_count((string) $response->getContent(), 'autocomplete="new-password"'),
            'The create form password field must hint new-password so password managers offer to generate one.'
        );
    }

    public function test_user_create_post_duplicate_email(): void
    {
        $data = [
            'email' => 'admin@example.com',
            'username' => 'test',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/create');
        $response->assertSessionHasErrors('email');
    }

    public function test_user_create_post_duplicate_username(): void
    {
        $data = [
            'email' => 'test@example.com',
            'username' => 'admin',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/create');
        $response->assertSessionHasErrors('username');
    }

    public function test_user_create_post(): void
    {
        $data = [
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'B3sTp@ss',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/edit/2');
        $response->assertSessionHas('success');
    }

    public function test_user_edit_get(): void
    {
        $user = User::firstOrFail();

        $response = $this->get('/user/edit/'.$user->id);

        $response->assertOk();
    }

    public function test_user_edit_get_password_fields_hint_new_password(): void
    {
        $user = User::firstOrFail();

        $response = $this->get('/user/edit/'.$user->id);

        $response->assertOk();

        $content = (string) $response->getContent();

        $this->assertSame(
            2,
            substr_count($content, 'autocomplete="new-password"'),
            'Both edit form password fields must hint new-password so password managers offer to generate one.'
        );
        $this->assertSame(
            2,
            substr_count($content, 'autocomplete="current-password"'),
            'The confirm password modal and the current password field must both hint current-password so password managers offer the existing one.'
        );
    }

    public function test_user_edit_post_duplicate_email(): void
    {
        $user = User::firstOrFail();

        // Create second user
        User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $data = [
            'email' => 'test@example.com',
            'username' => 'test',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('email');
    }

    public function test_user_edit_post_duplicate_username(): void
    {
        // Create second user
        $user = User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $data = [
            'email' => 'test@example.com',
            'username' => 'admin',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('username');
    }

    public function test_user_edit_post(): void
    {
        $user = User::firstOrFail();

        $data = [
            'email' => 'admin2@example.com',
            'username' => 'admin2',
        ];

        $response = $this->post('/user/edit/'.$user->id, $data);
        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHas('success');
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::firstOrFail();

        $response = $this->post('/user/edit/'.$user->id, [
            'email' => $user->email,
            'username' => $user->username,
            'password1' => 'N3wP@ssw0rd!',
            'password2' => 'N3wP@ssw0rd!',
        ]);

        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('admin', $user->password), 'The password must be unchanged.');
    }

    public function test_password_change_rejects_wrong_current_password(): void
    {
        $user = User::firstOrFail();

        $response = $this->post('/user/edit/'.$user->id, [
            'email' => $user->email,
            'username' => $user->username,
            'current_password' => 'not-my-password',
            'password1' => 'N3wP@ssw0rd!',
            'password2' => 'N3wP@ssw0rd!',
        ]);

        $response->assertRedirect('/user/edit/'.$user->id);
        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('admin', $user->password), 'The password must be unchanged.');
    }

    public function test_password_change_succeeds_with_correct_current_password(): void
    {
        $user = User::firstOrFail();

        $response = $this->post('/user/edit/'.$user->id, [
            'email' => $user->email,
            'username' => $user->username,
            'current_password' => 'admin',
            'password1' => 'N3wP@ssw0rd!',
            'password2' => 'N3wP@ssw0rd!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/user/edit/'.$user->id);

        $user->refresh();
        $this->assertTrue(Hash::check('N3wP@ssw0rd!', $user->password));
    }

    public function test_admin_changing_another_users_password_supplies_own_current_password(): void
    {
        $target = User::unguarded(fn () => User::create([
            'email' => 'target@example.com',
            'username' => 'target',
            'password' => 'targetpassword',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $perm = new UserPermission;
        $perm->user_id = $target->id;
        $perm->save();

        // `current_password` is the acting admin's own password, never the
        // target's — an admin cannot be expected to know the latter.
        $response = $this->post('/user/edit/'.$target->id, [
            'email' => $target->email,
            'username' => $target->username,
            'current_password' => 'admin',
            'password1' => 'N3wP@ssw0rd!',
            'password2' => 'N3wP@ssw0rd!',
        ]);

        $response->assertSessionHasNoErrors();

        $target->refresh();
        $this->assertTrue(Hash::check('N3wP@ssw0rd!', $target->password));
    }

    public function test_admin_changing_another_users_password_rejects_targets_password(): void
    {
        $target = User::unguarded(fn () => User::create([
            'email' => 'target2@example.com',
            'username' => 'target2',
            'password' => 'targetpassword',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $perm = new UserPermission;
        $perm->user_id = $target->id;
        $perm->save();

        $response = $this->post('/user/edit/'.$target->id, [
            'email' => $target->email,
            'username' => $target->username,
            'current_password' => 'targetpassword',
            'password1' => 'N3wP@ssw0rd!',
            'password2' => 'N3wP@ssw0rd!',
        ]);

        $response->assertSessionHasErrors('current_password');

        $target->refresh();
        $this->assertTrue(Hash::check('targetpassword', $target->password), 'The password must be unchanged.');
    }

    public function test_user_delete_get(): void
    {
        // Create second user
        $user = User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $perm = new UserPermission;
        $perm->user_id = $user->id;
        $perm->save();

        $response = $this->get('/user/delete/'.$user->id);
        $response->assertOk();
    }

    public function test_user_delete_get_invalid_id(): void
    {
        $response = $this->get('/user/delete/100000');
        $response->assertRedirect('/user/list');
    }

    public function test_user_delete_post_invalid_id(): void
    {
        $response = $this->post('/user/delete/100000');
        $response->assertRedirect('/user/list');
    }

    public function test_user_delete_post(): void
    {
        // Create second user
        $user = User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $perm = new UserPermission;
        $perm->user_id = $user->id;
        $perm->save();

        $response = $this->post('/user/delete/'.$user->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHas('success');
    }

    public function test_non_superadmin_cannot_delete_superadmin(): void
    {
        // Create second user
        $user = User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        // Allow this user to manage users, but not be an admin
        $perm = new UserPermission;
        $perm->user_id = $user->id;
        $perm->solder_users = 1;
        $perm->save();

        // Auth as the new user
        $this->be($user);

        // A non-superadmin may not act on a solder_full account at all, so the
        // delete is denied outright rather than reaching the last-admin guard
        // (which is exercised by test_user_cannot_delete_self for a superadmin).
        $response = $this->post('/user/delete/1');
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    public function test_user_cannot_delete_self(): void
    {
        $response = $this->post('/user/delete/'.auth()->user()->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => auth()->user()->id]);
    }

    public function test_non_superadmin_cannot_delete_self(): void
    {
        $user = User::unguarded(fn () => User::create([
            'email' => 'selfdelete@example.com',
            'username' => 'selfdelete',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        // Enough permission to reach the delete handler, but self-deletion is
        // refused for everyone regardless of privilege level.
        $perm = new UserPermission;
        $perm->user_id = $user->id;
        $perm->solder_users = 1;
        $perm->save();

        $this->be($user);

        $response = $this->post('/user/delete/'.$user->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_user_cannot_reach_self_delete_confirmation(): void
    {
        $response = $this->get('/user/delete/'.auth()->user()->id);
        $response->assertRedirect('/user/list');
        $response->assertSessionHasErrors();
    }

    public function test_user_create_more_superadmins_post(): void
    {
        $data = [
            'email' => 'test-sadmin@example.com',
            'username' => 'sadmin',
            'password' => 'B3sT3Re4p@ss',
            'solder-full' => '1',
        ];

        $response = $this->post('/user/create', $data);
        $response->assertRedirect('/user/edit/2');
        $response->assertSessionHas('success');
    }

    public function test_created_user_can_login(): void
    {
        $data = [
            'email' => 'logintest@example.com',
            'username' => 'logintest',
            'password' => 'TestPassword123',
        ];

        $this->post('/user/create', $data);
        auth()->logout();

        $response = $this->post('/login', [
            'email' => 'logintest@example.com',
            'password' => 'TestPassword123',
        ]);

        $response->assertRedirect('dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'logintest@example.com')->first());
    }

    public function test_user_delete_first_post(): void
    {
        // Create second user
        $user = User::unguarded(fn () => User::create([
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
            'created_by_user_id' => 1,
        ]));

        $perm = new UserPermission;
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
