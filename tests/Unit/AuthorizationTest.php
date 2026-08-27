<?php

namespace Tests\Unit;

use App\Models\Mod;
use App\Models\Modpack;
use App\Models\Modversion;
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
        $unique = uniqid();
        $user = new User;
        $user->username = 'testuser-'.$unique;
        $user->email = 'test-'.$unique.'@example.com';
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

    public function test_edit_denial_does_not_reveal_whether_user_exists(): void
    {
        $user = $this->createUserWithPermissions();
        $target = $this->createUserWithPermissions();

        // A non-manager must get the same denial for a real id as for an absent
        // one; a "User not found" error would enumerate the user table.
        $this->actingAs($user)->get('/user/edit/'.$target->id)
            ->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/user/edit/999999')
            ->assertRedirect('/dashboard');

        $this->actingAs($user)->post('/user/edit/'.$target->id)
            ->assertRedirect('/dashboard');
        $this->actingAs($user)->post('/user/edit/999999')
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
            'permissions-present' => '1',
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

    // --- Mod version write authorization ---
    //
    // These AJAX endpoints mutate modversions and must be gated by `mods_manage`
    // (via ModversionPolicy), not merely by the `mods_create` capability. A user
    // who can create mods but cannot manage them must be denied. The endpoints
    // abort(404) on non-AJAX requests, so the AJAX header is required to reach
    // the authorization gate (which then renders 403 for JSON requests).

    private function seedModVersion(): Modversion
    {
        $mod = Mod::create(['name' => 'authztest', 'pretty_name' => 'AuthZ Test']);

        return Modversion::create([
            'mod_id' => $mod->id,
            'version' => '1.0',
            'md5' => str_repeat('a', 32),
            'filesize' => 100,
        ]);
    }

    public function test_add_version_denied_without_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version', [
                'mod-id' => $version->mod_id,
                'add-version' => '2.0',
                'add-md5' => str_repeat('c', 32),
            ])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('modversions', [
            'mod_id' => $version->mod_id,
            'version' => '2.0',
        ]);
    }

    public function test_rehash_denied_without_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/rehash', [
                'version-id' => $version->id,
                'md5' => str_repeat('b', 32),
            ])
            ->assertRedirect('/dashboard');
    }

    public function test_update_version_denied_without_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/update-version/'.$version->id, ['notes' => 'tampered'])
            ->assertRedirect('/dashboard');
    }

    public function test_delete_version_denied_without_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_create' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/delete-version/'.$version->id)
            ->assertRedirect('/dashboard');

        $this->assertModelExists($version);
    }

    public function test_add_version_allowed_with_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_manage' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/add-version', [
                'mod-id' => $version->mod_id,
                'add-version' => '2.0',
                'add-md5' => str_repeat('c', 32),
            ])
            ->assertOk()
            ->assertJson(['status' => 'success']);
    }

    public function test_delete_version_allowed_with_mods_manage(): void
    {
        $user = $this->createUserWithPermissions(['mods_manage' => true]);
        $version = $this->seedModVersion();

        $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('/mod/delete-version/'.$version->id)
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertModelMissing($version);
    }

    // --- User permission delegation (clamp) ---
    //
    // A solder_users delegate may only grant permissions it holds itself; only
    // solder_full may grant arbitrarily. A delegate also cannot act on a
    // more-privileged account (UserPolicy subset check).

    public function test_manager_cannot_grant_permissions_it_lacks_on_create(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true, 'mods_manage' => true]);

        $this->actingAs($actor)->post('/user/create', [
            'permissions-present' => '1',
            'username' => 'delegated',
            'email' => 'delegated@example.com',
            'password' => 'B3sTp@ss',
            'manage-users' => 'on', // actor holds solder_users -> granted
            'mod-manage' => 'on',   // actor holds mods_manage -> granted
            'mod-delete' => 'on',   // actor lacks mods_delete -> clamped
            'manage-keys' => 'on',  // actor lacks solder_keys -> clamped
            'solder-full' => 'on',  // never grantable by a non-superadmin
        ]);

        $perm = User::where('email', 'delegated@example.com')->firstOrFail()->permission;
        $this->assertTrue((bool) $perm->solder_users);
        $this->assertTrue((bool) $perm->mods_manage);
        $this->assertFalse((bool) $perm->mods_delete);
        $this->assertFalse((bool) $perm->solder_keys);
        $this->assertFalse((bool) $perm->solder_full);
    }

    public function test_manager_cannot_grant_permissions_it_lacks_on_edit(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true, 'mods_manage' => true]);
        $target = $this->createUserWithPermissions(['mods_manage' => true]);

        $this->actingAs($actor)->post('/user/edit/'.$target->id, [
            'permissions-present' => '1',
            'username' => $target->username,
            'email' => $target->email,
            'mod-manage' => 'on',
            'mod-delete' => 'on',  // actor lacks -> must not be granted
            'manage-keys' => 'on', // actor lacks -> must not be granted
        ]);

        $perm = $target->fresh()->permission;
        $this->assertTrue((bool) $perm->mods_manage);
        $this->assertFalse((bool) $perm->mods_delete);
        $this->assertFalse((bool) $perm->solder_keys);
    }

    public function test_manager_cannot_escalate_own_permissions_on_self_edit(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true, 'mods_manage' => true]);

        $this->actingAs($actor)->post('/user/edit/'.$actor->id, [
            'permissions-present' => '1',
            'username' => $actor->username,
            'email' => $actor->email,
            'manage-users' => 'on',
            'mod-manage' => 'on',
            'mod-delete' => 'on',  // self-grant must be clamped
            'solder-full' => 'on', // self-promotion must be clamped
        ]);

        $perm = $actor->fresh()->permission;
        $this->assertTrue((bool) $perm->solder_users);
        $this->assertTrue((bool) $perm->mods_manage);
        $this->assertFalse((bool) $perm->mods_delete);
        $this->assertFalse((bool) $perm->solder_full);
    }

    public function test_manager_cannot_grant_modpacks_outside_own_scope(): void
    {
        $packA = Modpack::create(['name' => 'Clamp Pack A', 'slug' => 'clamp-pack-a']);
        $packB = Modpack::create(['name' => 'Clamp Pack B', 'slug' => 'clamp-pack-b']);

        $actor = $this->createUserWithPermissions(['solder_users' => true, 'modpacks_manage' => true]);
        $actor->permission->modpacks = [$packA->id];
        $actor->permission->save();
        $actor->load('permission');

        $this->actingAs($actor)->post('/user/create', [
            'permissions-present' => '1',
            'username' => 'scoped',
            'email' => 'scoped@example.com',
            'password' => 'B3sTp@ss',
            'modpack-manage' => 'on',
            'modpack' => [$packA->id, $packB->id],
        ]);

        $packs = array_map('intval', User::where('email', 'scoped@example.com')->firstOrFail()->permission->modpacks);
        $this->assertContains((int) $packA->id, $packs);
        $this->assertNotContains((int) $packB->id, $packs);
    }

    public function test_manager_cannot_edit_more_privileged_user(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);

        // User 1 is the seeded solder_full superadmin.
        $this->actingAs($actor)->get('/user/edit/1')->assertRedirect('/dashboard');
        $this->actingAs($actor)->post('/user/edit/1', [
            'username' => 'admin',
            'email' => 'admin@example.com',
        ])->assertRedirect('/dashboard');
    }

    public function test_manager_cannot_edit_user_with_extra_permissions(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);
        $target = $this->createUserWithPermissions(['solder_users' => true, 'mods_delete' => true]);

        $this->actingAs($actor)->post('/user/edit/'.$target->id, [
            'username' => $target->username,
            'email' => $target->email,
        ])->assertRedirect('/dashboard');
    }

    public function test_solder_full_can_grant_any_permission(): void
    {
        $admin = User::find(1); // solder_full

        $this->actingAs($admin)->post('/user/create', [
            'permissions-present' => '1',
            'username' => 'fullgrant',
            'email' => 'fullgrant@example.com',
            'password' => 'B3sTp@ss',
            'mod-delete' => 'on',
            'modpack-delete' => 'on',
            'manage-keys' => 'on',
        ]);

        $perm = User::where('email', 'fullgrant@example.com')->firstOrFail()->permission;
        $this->assertTrue((bool) $perm->mods_delete);
        $this->assertTrue((bool) $perm->modpacks_delete);
        $this->assertTrue((bool) $perm->solder_keys);
    }

    public function test_manager_cannot_reset_2fa_of_more_privileged_user(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);

        $this->actingAs($actor)->post('/user/1/reset-2fa')->assertRedirect('/dashboard');
    }

    public function test_manager_can_reset_2fa_of_managed_user(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);
        $target = $this->createUserWithPermissions();
        $target->forceFill([
            'two_factor_secret' => 'enrolled-secret',
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->actingAs($actor)->post('/user/'.$target->id.'/reset-2fa')
            ->assertRedirect('/user/edit/'.$target->id);

        $target->refresh();
        $this->assertNull($target->two_factor_secret);
        $this->assertNull($target->two_factor_confirmed_at);
    }

    public function test_regular_user_cannot_reset_own_2fa(): void
    {
        $user = $this->createUserWithPermissions();

        $this->actingAs($user)->post('/user/'.$user->id.'/reset-2fa')
            ->assertRedirect('/dashboard');
    }

    public function test_regular_user_cannot_delete_users(): void
    {
        $user = $this->createUserWithPermissions();
        $target = $this->createUserWithPermissions();

        $this->actingAs($user)->post('/user/delete/'.$target->id)
            ->assertRedirect('/dashboard');
        $this->assertModelExists($target);
    }

    public function test_manager_cannot_delete_more_privileged_user(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);

        // User 1 is the seeded solder_full superadmin.
        $this->actingAs($actor)->post('/user/delete/1')->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    public function test_manager_cannot_delete_user_with_extra_permissions(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);
        $target = $this->createUserWithPermissions(['solder_users' => true, 'mods_delete' => true]);

        $this->actingAs($actor)->post('/user/delete/'.$target->id)
            ->assertRedirect('/dashboard');
        $this->assertModelExists($target);
    }

    public function test_manager_can_delete_managed_user(): void
    {
        $actor = $this->createUserWithPermissions(['solder_users' => true]);
        $target = $this->createUserWithPermissions();

        $this->actingAs($actor)->post('/user/delete/'.$target->id)
            ->assertRedirect('/user/list');
        $this->assertModelMissing($target);
    }
}
