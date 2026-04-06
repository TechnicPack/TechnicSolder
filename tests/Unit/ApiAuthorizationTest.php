<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithToken(array $permissions = []): string
    {
        $user = User::create([
            'username' => 'testuser-'.uniqid(),
            'email' => uniqid().'@example.com',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
        ]);

        UserPermission::create(array_merge([
            'user_id' => $user->id,
            'solder_full' => false,
        ], $permissions));

        return $user->createToken('test')->plainTextToken;
    }

    private function headers(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // --- No token ---

    public function test_no_token_returns_401(): void
    {
        $this->postJson('api/modpack', ['name' => 'Test', 'slug' => 'test'])->assertStatus(401);
    }

    public function test_invalid_token_returns_401(): void
    {
        $this->postJson('api/modpack', ['name' => 'Test', 'slug' => 'test'], [
            'Authorization' => 'Bearer invalid',
        ])->assertStatus(401);
    }

    // --- Modpack authorization ---

    public function test_create_modpack_with_permission(): void
    {
        $token = $this->createUserWithToken(['modpacks_create' => true]);

        $this->postJson('api/modpack', [
            'name' => 'Auth Pack',
            'slug' => 'auth-pack',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_create_modpack_without_permission(): void
    {
        $token = $this->createUserWithToken();

        $this->postJson('api/modpack', [
            'name' => 'Auth Pack',
            'slug' => 'auth-pack',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_update_modpack_with_permission_and_access(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);

        $this->putJson('api/modpack/'.$modpack->slug, [
            'name' => 'Updated',
        ], $this->headers($token))->assertOk();
    }

    public function test_update_modpack_with_permission_but_no_access(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken(['modpacks_manage' => true]);

        $this->putJson('api/modpack/'.$modpack->slug, [
            'name' => 'Updated',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_delete_modpack_with_permission_and_access(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken([
            'modpacks_delete' => true,
            'modpacks' => [$modpack->id],
        ]);

        $this->deleteJson('api/modpack/'.$modpack->slug, [], $this->headers($token))->assertOk();
    }

    public function test_delete_modpack_without_permission(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken();

        $this->deleteJson('api/modpack/'.$modpack->slug, [], $this->headers($token))->assertStatus(403);
    }

    // --- Build authorization (uses parent modpack access) ---

    public function test_create_build_with_modpack_access(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);

        $this->postJson('api/modpack/'.$modpack->slug.'/build', [
            'version' => '9.0.0',
            'minecraft' => '1.20.1',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_create_build_without_modpack_access(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken(['modpacks_manage' => true]);

        $this->postJson('api/modpack/'.$modpack->slug.'/build', [
            'version' => '9.0.0',
            'minecraft' => '1.20.1',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_delete_build_without_permission(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();
        $token = $this->createUserWithToken();

        $this->deleteJson(
            'api/modpack/'.$modpack->slug.'/'.$build->version,
            [],
            $this->headers($token)
        )->assertStatus(403);
    }

    // --- Mod authorization ---

    public function test_create_mod_with_permission(): void
    {
        $token = $this->createUserWithToken(['mods_create' => true]);

        $this->postJson('api/mod', [
            'name' => 'auth-mod',
            'pretty_name' => 'Auth Mod',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_create_mod_without_permission(): void
    {
        $token = $this->createUserWithToken();

        $this->postJson('api/mod', [
            'name' => 'auth-mod',
            'pretty_name' => 'Auth Mod',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_update_mod_with_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken(['mods_manage' => true]);

        $this->putJson('api/mod/'.$mod->name, [
            'pretty_name' => 'Updated',
        ], $this->headers($token))->assertOk();
    }

    public function test_update_mod_without_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken();

        $this->putJson('api/mod/'.$mod->name, [
            'pretty_name' => 'Updated',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_delete_mod_with_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken(['mods_delete' => true]);

        $this->deleteJson('api/mod/'.$mod->name, [], $this->headers($token))->assertOk();
    }

    public function test_delete_mod_without_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken();

        $this->deleteJson('api/mod/'.$mod->name, [], $this->headers($token))->assertStatus(403);
    }

    // --- Modversion authorization ---

    public function test_create_modversion_with_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken(['mods_manage' => true]);

        $this->postJson('api/mod/'.$mod->name.'/version', [
            'version' => '9.0.0',
            'md5' => 'abc123',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_create_modversion_without_permission(): void
    {
        $mod = Mod::first();
        $token = $this->createUserWithToken();

        $this->postJson('api/mod/'.$mod->name.'/version', [
            'version' => '9.0.0',
            'md5' => 'abc123',
        ], $this->headers($token))->assertStatus(403);
    }

    // --- Client authorization ---

    public function test_create_client_with_permission(): void
    {
        $token = $this->createUserWithToken(['solder_clients' => true]);

        $this->postJson('api/client', [
            'name' => 'Auth Client',
            'uuid' => 'auth-client-uuid',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_create_client_without_permission(): void
    {
        $token = $this->createUserWithToken();

        $this->postJson('api/client', [
            'name' => 'Auth Client',
            'uuid' => 'auth-client-uuid',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_delete_client_without_permission(): void
    {
        $client = Client::first();
        $token = $this->createUserWithToken();

        $this->deleteJson('api/client/'.$client->uuid, [], $this->headers($token))->assertStatus(403);
    }

    // --- Token management (any authenticated user) ---

    public function test_any_user_can_list_own_tokens(): void
    {
        $token = $this->createUserWithToken();
        $this->getJson('api/token', $this->headers($token))->assertOk();
    }

    public function test_any_user_can_create_token(): void
    {
        $token = $this->createUserWithToken();
        $this->postJson('api/token', ['name' => 'My Token'], $this->headers($token))->assertStatus(201);
    }

    // --- solder_full bypasses everything ---

    public function test_solder_full_bypasses_modpack_create(): void
    {
        $token = $this->createUserWithToken(['solder_full' => true]);

        $this->postJson('api/modpack', [
            'name' => 'Full Pack',
            'slug' => 'full-pack',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_solder_full_bypasses_mod_create(): void
    {
        $token = $this->createUserWithToken(['solder_full' => true]);

        $this->postJson('api/mod', [
            'name' => 'full-mod',
            'pretty_name' => 'Full Mod',
        ], $this->headers($token))->assertStatus(201);
    }

    public function test_solder_full_bypasses_modpack_access_check(): void
    {
        $modpack = Modpack::first();
        $token = $this->createUserWithToken(['solder_full' => true]);

        $this->putJson('api/modpack/'.$modpack->slug, [
            'name' => 'Full Update',
        ], $this->headers($token))->assertOk();
    }

    public function test_solder_full_bypasses_client_permission(): void
    {
        $token = $this->createUserWithToken(['solder_full' => true]);

        $this->postJson('api/client', [
            'name' => 'Full Client',
            'uuid' => 'full-client-uuid',
        ], $this->headers($token))->assertStatus(201);
    }

    // --- Cross-cutting: wrong permission doesn't grant access ---

    public function test_mods_permission_does_not_grant_modpack_access(): void
    {
        $token = $this->createUserWithToken([
            'mods_create' => true,
            'mods_manage' => true,
            'mods_delete' => true,
        ]);

        $this->postJson('api/modpack', [
            'name' => 'Cross Pack',
            'slug' => 'cross-pack',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_modpack_permission_does_not_grant_mod_access(): void
    {
        $token = $this->createUserWithToken([
            'modpacks_create' => true,
            'modpacks_manage' => true,
        ]);

        $this->postJson('api/mod', [
            'name' => 'cross-mod',
            'pretty_name' => 'Cross Mod',
        ], $this->headers($token))->assertStatus(403);
    }

    public function test_client_permission_does_not_grant_mod_access(): void
    {
        $token = $this->createUserWithToken(['solder_clients' => true]);

        $this->postJson('api/mod', [
            'name' => 'cross-mod-2',
            'pretty_name' => 'Cross Mod 2',
        ], $this->headers($token))->assertStatus(403);
    }
}
