<?php

namespace Tests\Unit;

use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function createUserWithPermissions(array $perms = []): User
    {
        $user = new User;
        $user->username = 'testuser';
        $user->email = 'test@example.com';
        $user->password = 'password';
        $user->created_ip = '127.0.0.1';
        $user->created_by_user_id = 1;
        $user->updated_by_user_id = 1;
        $user->updated_by_ip = '127.0.0.1';
        $user->save();

        $permission = new UserPermission;
        $permission->user_id = $user->id;
        foreach ($perms as $key => $value) {
            $permission->{$key} = $value;
        }
        $permission->save();

        $user->load('permission');

        return $user;
    }

    // --- Guest access tests ---

    public function test_guest_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_mod_routes(): void
    {
        $this->get('/mod/list')->assertRedirect('/login');
        $this->get('/mod/create')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_modpack_routes(): void
    {
        $this->get('/modpack/list')->assertRedirect('/login');
        $this->get('/modpack/create')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_key_routes(): void
    {
        $this->get('/key/list')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_client_routes(): void
    {
        $this->get('/client/list')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_user_routes(): void
    {
        $this->get('/user/list')->assertRedirect('/login');
    }

    public function test_guest_redirected_from_solder_routes(): void
    {
        $this->get('/solder/configure')->assertRedirect('/login');
    }

    // --- Mod permissions ---

    public function test_user_with_mods_create_can_access_mod_create(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $this->actingAs($user)->get('/mod/create')->assertOk();
    }

    public function test_user_with_mods_create_cannot_access_mod_delete(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $this->actingAs($user)->get('/mod/delete/1')->assertRedirect('/dashboard');
    }

    public function test_user_with_mods_manage_can_access_mod_list(): void
    {
        $user = $this->createUserWithPermissions(['mods_manage' => true]);
        $this->actingAs($user)->get('/mod/list')->assertOk();
    }

    public function test_user_with_mods_manage_cannot_access_mod_create(): void
    {
        $user = $this->createUserWithPermissions(['mods_manage' => true]);
        $this->actingAs($user)->get('/mod/create')->assertRedirect('/dashboard');
    }

    public function test_user_with_mods_delete_can_access_mod_delete(): void
    {
        $user = $this->createUserWithPermissions(['mods_delete' => true]);
        // mod/delete/1 exists from seed
        $this->actingAs($user)->get('/mod/delete/1')->assertOk();
    }

    public function test_user_with_no_mod_perms_redirected(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/mod/list')
            ->assertRedirect('/dashboard');
    }

    // --- Modpack permissions ---

    public function test_user_with_modpacks_create_can_access_modpack_create(): void
    {
        $user = $this->createUserWithPermissions(['modpacks_create' => true]);
        $this->actingAs($user)->get('/modpack/create')->assertOk();
    }

    public function test_user_with_modpacks_create_cannot_access_modpack_edit(): void
    {
        $user = $this->createUserWithPermissions(['modpacks_create' => true]);
        $this->actingAs($user)->get('/modpack/edit/1')->assertRedirect('/dashboard');
    }

    public function test_user_with_modpacks_manage_can_access_modpack_list(): void
    {
        $user = $this->createUserWithPermissions(['modpacks_manage' => true]);
        $this->actingAs($user)->get('/modpack/list')->assertOk();
    }

    public function test_user_with_modpacks_delete_cannot_access_modpack_list(): void
    {
        $user = $this->createUserWithPermissions(['modpacks_delete' => true]);
        // modpack/delete route uses the 'delete' segment, but modpack/list uses 'modpacks_manage' default
        $this->actingAs($user)->get('/modpack/list')->assertRedirect('/dashboard');
    }

    // --- Key permissions ---

    public function test_user_with_solder_keys_can_access_key_routes(): void
    {
        $user = $this->createUserWithPermissions(['solder_keys' => true]);
        $this->actingAs($user)->get('/key/list')->assertOk();
    }

    public function test_user_without_solder_keys_redirected(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/key/list')
            ->assertRedirect('/dashboard');
    }

    // --- Client permissions ---

    public function test_user_with_solder_clients_can_access_client_routes(): void
    {
        $user = $this->createUserWithPermissions(['solder_clients' => true]);
        $this->actingAs($user)->get('/client/list')->assertOk();
    }

    public function test_user_without_solder_clients_redirected(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/client/list')
            ->assertRedirect('/dashboard');
    }

    // --- User permissions ---

    public function test_user_with_solder_users_can_access_user_list(): void
    {
        $user = $this->createUserWithPermissions(['solder_users' => true]);
        $this->actingAs($user)->get('/user/list')->assertOk();
    }

    public function test_regular_user_can_edit_own_profile(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/user/edit/'.$user->id)->assertOk();
    }

    public function test_regular_user_cannot_edit_others(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/user/edit/1')
            ->assertRedirect('/dashboard');
    }

    public function test_regular_user_cannot_access_user_list(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->get('/user/list')
            ->assertRedirect('/dashboard');
    }

    // --- Solder full bypasses all ---

    public function test_solder_full_can_access_all_routes(): void
    {
        $user = User::find(1); // admin with solder_full
        $this->actingAs($user);

        $this->get('/mod/list')->assertOk();
        $this->get('/mod/create')->assertOk();
        $this->get('/modpack/list')->assertOk();
        $this->get('/modpack/create')->assertOk();
        $this->get('/key/list')->assertOk();
        $this->get('/client/list')->assertOk();
        $this->get('/user/list')->assertOk();
    }

    // --- Per-modpack access ---

    public function test_user_with_modpacks_manage_but_no_modpack_access_redirected(): void
    {
        $modpack = Modpack::first();
        $user = $this->createUserWithPermissions(['modpacks_manage' => true]);
        // No modpacks in allowed list
        $this->actingAs($user)->get('/modpack/view/'.$modpack->id)
            ->assertRedirect('/dashboard');
    }

    public function test_user_with_modpacks_manage_and_modpack_access_can_view(): void
    {
        $modpack = Modpack::first();
        $user = $this->createUserWithPermissions(['modpacks_manage' => true]);
        $user->permission->modpacks = [$modpack->id];
        $user->permission->save();
        $this->actingAs($user)->get('/modpack/view/'.$modpack->id)->assertOk();
    }

    public function test_build_access_follows_parent_modpack(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();
        $user = $this->createUserWithPermissions(['modpacks_manage' => true]);
        // No modpack access → can't view build
        $this->actingAs($user)->get('/modpack/build/'.$build->id)
            ->assertRedirect('/dashboard');
    }

    public function test_solder_full_bypasses_per_modpack_access(): void
    {
        $modpack = Modpack::first();
        $user = User::find(1); // solder_full
        $this->actingAs($user)->get('/modpack/view/'.$modpack->id)->assertOk();
    }

    // --- User self-edit edge cases ---

    public function test_user_self_edit_cannot_change_own_permissions(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user)->post('/user/edit/'.$user->id, [
            'email' => $user->email,
            'username' => $user->username,
            'solder-full' => 'on',
        ]);
        $user->refresh();
        $this->assertFalse((bool) $user->permission->solder_full);
    }

    // --- Cross-cutting ---

    public function test_user_with_mods_manage_cannot_access_modpack_routes(): void
    {
        $user = $this->createUserWithPermissions(['mods_manage' => true]);
        $this->actingAs($user)->get('/modpack/list')
            ->assertRedirect('/dashboard');
    }

    public function test_user_with_solder_clients_cannot_access_key_routes(): void
    {
        $user = $this->createUserWithPermissions(['solder_clients' => true]);
        $this->actingAs($user)->get('/key/list')
            ->assertRedirect('/dashboard');
    }

    public function test_user_with_no_permissions_has_dashboard_only(): void
    {
        $user = $this->createUserWithPermissions();
        $this->actingAs($user);

        $this->get('/dashboard')->assertOk();
        $this->get('/mod/list')->assertRedirect('/dashboard');
        $this->get('/modpack/list')->assertRedirect('/dashboard');
        $this->get('/key/list')->assertRedirect('/dashboard');
        $this->get('/client/list')->assertRedirect('/dashboard');
        $this->get('/user/list')->assertRedirect('/dashboard');
    }
}
