<?php

namespace Tests\Unit;

use App\Models\Build;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_base(): void
    {
        $response = $this->get('api/');
        $response->assertOk();
        $response->assertJson([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM,
        ]);
    }

    public function test_modpack(): void
    {
        $response = $this->get('api/modpack');
        $response->assertOk();
        $response->assertJsonStructure(['modpacks', 'mirror_url']);
    }

    public function test_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod');
        $response->assertOk();
        $response->assertJsonStructure(['mods']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_invalid_modpack(): void
    {
        $response = $this->get('api/modpack/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_modpack_slug(): void
    {
        $modpack = Modpack::find(1);
        $response = $this->get('api/modpack/'.$modpack->slug);
        $response->assertOk();
        $response->assertJsonStructure([
            'name',
            'display_name',
            'url',
            'icon',
            'icon_md5',
            'latest',
            'logo',
            'logo_md5',
            'recommended',
            'background',
            'background_md5',
            'builds',
        ]);
        $response->assertJsonPath('builds', ['1.0.0'], true);
    }

    public function test_invalid_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/bob');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_mod_slug(): void
    {
        $mod = Mod::find(1);

        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/'.$mod->name);
        $response->assertOk();
        $response->assertJsonStructure([
            'name',
            'pretty_name',
            'author',
            'description',
            'link',
            'versions',
        ]);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_modpack_build(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $response = $this->get('api/modpack/'.$modpack->slug.'/'.$build->version);
        $response->assertOk();
        $response->assertJsonStructure([
            'minecraft',
            'forge',
            'java',
            'memory',
            'mods',
        ]);
    }

    public function test_private_build_unauthorized(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->private = true;
        $build->save();

        $response = $this->get('api/modpack/'.$modpack->slug.'/'.$build->version);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Build does not exist']);
    }

    public function test_mod_version(): void
    {
        $mod = Mod::find(1);
        $modversion = $mod->versions->first();

        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/'.$mod->name.'/'.$modversion->version);
        $response->assertOk();
        $response->assertJson([
            'md5' => $modversion->md5,
            'filesize' => $modversion->filesize,
            'url' => $modversion->url,
        ]);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name.'/'.$modversion->version);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_modversion_with_invalid_mod(): void
    {
        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/foo/bar');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/foo/bar');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    public function test_invalid_modversion(): void
    {
        $mod = Mod::find(1);

        config()->set('solder.disable_mod_api', false);
        $response = $this->get('api/mod/'.$mod->name.'/invalid');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod version does not exist']);

        config()->set('solder.disable_mod_api', true);
        $response = $this->get('api/mod/'.$mod->name.'/invalid');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Mod API has been disabled']);
    }

    // --- Sanctum user access tests ---

    private function makePrivateModpack(): Modpack
    {
        return Modpack::create([
            'name' => 'PrivatePack',
            'slug' => 'privatepack',
            'hidden' => true,
            'private' => true,
            'icon' => false,
            'icon_md5' => null,
            'icon_url' => '',
            'logo' => false,
            'logo_md5' => null,
            'logo_url' => '',
            'background' => false,
            'background_md5' => null,
            'background_url' => '',
        ]);
    }

    private function makeHiddenModpack(): Modpack
    {
        $modpack = Modpack::create([
            'name' => 'HiddenPack',
            'slug' => 'hiddenpack',
            'hidden' => true,
            'private' => false,
            'icon' => false,
            'icon_md5' => null,
            'icon_url' => '',
            'logo' => false,
            'logo_md5' => null,
            'logo_url' => '',
            'background' => false,
            'background_md5' => null,
            'background_url' => '',
        ]);

        Build::create([
            'modpack_id' => $modpack->id,
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'min_java' => '1.7',
            'min_memory' => '1024',
            'is_published' => true,
        ]);

        return $modpack;
    }

    private function makeUserWithPermissions(array $perms): User
    {
        $user = User::create([
            'username' => 'testuser_'.uniqid(),
            'email' => uniqid().'@example.com',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
            'last_ip' => '127.0.0.1',
        ]);

        UserPermission::create(array_merge(['user_id' => $user->id], $perms));

        return $user->fresh();
    }

    public function test_sanctum_user_with_full_access_sees_private_modpacks(): void
    {
        $this->makePrivateModpack();
        $user = User::find(1); // admin with solder_full
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonFragment(['privatepack' => 'PrivatePack']);
    }

    public function test_sanctum_user_with_modpack_access_sees_private_modpack(): void
    {
        $modpack = $this->makePrivateModpack();
        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonFragment(['privatepack' => 'PrivatePack']);
    }

    public function test_sanctum_user_without_modpack_access_cannot_see_private_modpack(): void
    {
        $this->makePrivateModpack();
        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonMissing(['privatepack' => 'PrivatePack']);
    }

    public function test_unauthenticated_cannot_see_private_modpack(): void
    {
        $this->makePrivateModpack();

        $response = $this->getJson('api/modpack');
        $response->assertOk();
        $response->assertJsonMissing(['privatepack' => 'PrivatePack']);
    }

    public function test_unauthenticated_cannot_access_private_modpack_directly(): void
    {
        $this->makePrivateModpack();

        $response = $this->getJson('api/modpack/privatepack');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_sanctum_user_with_access_can_access_private_modpack_directly(): void
    {
        $modpack = $this->makePrivateModpack();
        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/privatepack', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonPath('name', 'privatepack');
    }

    public function test_unauthenticated_cannot_access_build_on_private_modpack(): void
    {
        $modpack = $this->makePrivateModpack();
        Build::create([
            'modpack_id' => $modpack->id,
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'min_java' => '1.7',
            'min_memory' => '1024',
            'is_published' => true,
        ]);

        $response = $this->getJson('api/modpack/privatepack/1.0.0');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_cached_build_on_private_modpack_still_requires_access(): void
    {
        $modpack = $this->makePrivateModpack();
        Build::create([
            'modpack_id' => $modpack->id,
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'min_java' => '1.7',
            'min_memory' => '1024',
            'is_published' => true,
        ]);

        // Warm the cache as an authorized user
        $user = User::find(1);
        $token = $user->createToken('test')->plainTextToken;
        $response = $this->getJson('api/modpack/privatepack/1.0.0', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonStructure(['minecraft', 'mods']);

        // Reset the Sanctum guard's cached user so the next request is unauthenticated
        auth('sanctum')->forgetUser();

        // Now access without auth — should still be denied despite cache
        $response = $this->getJson('api/modpack/privatepack/1.0.0');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    public function test_cached_private_modpack_still_requires_access(): void
    {
        $this->makePrivateModpack();
        $user = User::find(1);
        $token = $user->createToken('test')->plainTextToken;

        // Warm the cache as an authorized user
        $response = $this->getJson('api/modpack/privatepack', ['Authorization' => 'Bearer '.$token]);
        $response->assertOk();
        $response->assertJsonPath('name', 'privatepack');

        // Reset the Sanctum guard's cached user so the next request is unauthenticated
        auth('sanctum')->forgetUser();

        // Now access without auth — should still be denied despite cache
        $response = $this->getJson('api/modpack/privatepack');
        $response->assertNotFound();
        $response->assertJson(['error' => 'Modpack does not exist']);
    }

    // --- Hidden (but not private) modpack: unlisted, but URL-accessible ---
    // Contract (per Modpack edit page UI): "Hidden modpacks will not show up
    // in the API response for the modpack list. However, anyone with the
    // modpack's slug can access all of its information."

    public function test_hidden_modpack_is_excluded_from_listing_without_auth(): void
    {
        $this->makeHiddenModpack();

        $response = $this->getJson('api/modpack');
        $response->assertOk();
        $response->assertJsonMissing(['hiddenpack' => 'HiddenPack']);
    }

    public function test_hidden_modpack_is_accessible_directly_by_slug_without_auth(): void
    {
        $this->makeHiddenModpack();

        $response = $this->getJson('api/modpack/hiddenpack');
        $response->assertOk();
        $response->assertJsonPath('name', 'hiddenpack');
        $response->assertJsonPath('display_name', 'HiddenPack');
        $response->assertJsonFragment(['builds' => ['1.0.0']]);
    }

    public function test_hidden_modpack_build_is_accessible_directly_without_auth(): void
    {
        $this->makeHiddenModpack();

        $response = $this->getJson('api/modpack/hiddenpack/1.0.0');
        $response->assertOk();
        $response->assertJsonStructure(['minecraft', 'forge', 'java', 'memory', 'mods']);
    }

    public function test_sanctum_user_with_full_access_sees_private_builds(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->update(['private' => true]);

        $user = User::find(1);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/'.$modpack->slug.'/'.$build->version, [
            'Authorization' => 'Bearer '.$token,
        ]);
        $response->assertOk();
        $response->assertJsonStructure(['minecraft', 'mods']);
    }

    public function test_sanctum_user_with_modpack_access_sees_private_builds(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->update(['private' => true]);

        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/'.$modpack->slug.'/'.$build->version, [
            'Authorization' => 'Bearer '.$token,
        ]);
        $response->assertOk();
        $response->assertJsonStructure(['minecraft', 'mods']);
    }

    public function test_sanctum_user_without_modpack_access_cannot_see_private_builds(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->update(['private' => true]);

        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/'.$modpack->slug.'/'.$build->version, [
            'Authorization' => 'Bearer '.$token,
        ]);
        $response->assertNotFound();
        $response->assertJson(['error' => 'Build does not exist']);
    }

    public function test_sanctum_user_with_modpack_access_sees_private_builds_in_modpack_response(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->update(['private' => true]);

        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [$modpack->id],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/'.$modpack->slug, [
            'Authorization' => 'Bearer '.$token,
        ]);
        $response->assertOk();
        $response->assertJsonFragment(['builds' => [$build->version]]);
    }

    public function test_sanctum_user_without_modpack_access_cannot_see_private_builds_in_modpack_response(): void
    {
        $modpack = Modpack::find(1);
        $build = $modpack->builds->first();
        $build->update(['private' => true]);

        $user = $this->makeUserWithPermissions([
            'modpacks_manage' => true,
            'modpacks' => [],
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/modpack/'.$modpack->slug, [
            'Authorization' => 'Bearer '.$token,
        ]);
        $response->assertOk();
        $response->assertJsonFragment(['builds' => []]);
    }

    public function test_mod_notes_visible_to_admin_with_token(): void
    {
        config()->set('solder.disable_mod_api', false);

        $mod = Mod::first();
        $mod->notes = 'Admin-only note';
        $mod->save();
        Cache::forget('mod:'.$mod->name);

        $admin = User::find(1);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->getJson('api/mod/'.$mod->name, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['notes' => 'Admin-only note']);
    }

    public function test_mod_notes_hidden_without_token(): void
    {
        config()->set('solder.disable_mod_api', false);

        $mod = Mod::first();
        $mod->notes = 'Secret note';
        $mod->save();
        Cache::forget('mod:'.$mod->name);

        $response = $this->getJson('api/mod/'.$mod->name);

        $response->assertOk();
        $this->assertArrayNotHasKey('notes', $response->json());
    }

    public function test_mod_notes_hidden_from_unprivileged_user(): void
    {
        config()->set('solder.disable_mod_api', false);

        $mod = Mod::first();
        $mod->notes = 'Secret note';
        $mod->save();
        Cache::forget('mod:'.$mod->name);

        $user = User::create([
            'username' => 'viewer',
            'email' => 'viewer@test.com',
            'password' => 'password',
            'created_ip' => '127.0.0.1',
        ]);
        $user->permission()->create(['solder_full' => false, 'mods_manage' => false]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('api/mod/'.$mod->name, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertOk();
        $this->assertArrayNotHasKey('notes', $response->json());
    }
}
