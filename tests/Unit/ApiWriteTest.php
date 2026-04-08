<?php

namespace Tests\Unit;

use App\Models\Build;
use App\Models\Client;
use App\Models\Key;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApiWriteTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->token = $user->createToken('test')->plainTextToken;
    }

    private function writeHeaders(): array
    {
        return ['Authorization' => 'Bearer '.$this->token];
    }

    // --- Auth tests ---

    public function test_request_without_auth_returns_401(): void
    {
        $response = $this->postJson('api/modpack', ['name' => 'Test', 'slug' => 'test']);
        $response->assertStatus(401);
    }

    public function test_request_with_invalid_token_returns_401(): void
    {
        $response = $this->postJson('api/modpack', ['name' => 'Test', 'slug' => 'test'], [
            'Authorization' => 'Bearer invalid-token',
        ]);
        $response->assertStatus(401);
    }

    public function test_request_with_valid_token_succeeds(): void
    {
        $response = $this->postJson('api/modpack', ['name' => 'New Pack', 'slug' => 'new-pack'], $this->writeHeaders());
        $response->assertStatus(201);
    }

    // --- Modpacks CRUD ---

    public function test_create_modpack(): void
    {
        $response = $this->postJson('api/modpack', [
            'name' => 'API Pack',
            'slug' => 'api-pack',
        ], $this->writeHeaders());

        $response->assertStatus(201);
        $this->assertDatabaseHas('modpacks', ['slug' => 'api-pack']);
    }

    public function test_create_modpack_duplicate_slug(): void
    {
        $modpack = Modpack::first();
        $response = $this->postJson('api/modpack', [
            'name' => 'Other',
            'slug' => $modpack->slug,
        ], $this->writeHeaders());

        $response->assertStatus(422);
    }

    public function test_create_modpack_missing_fields(): void
    {
        $response = $this->postJson('api/modpack', [], $this->writeHeaders());
        $response->assertStatus(422);
    }

    public function test_update_modpack(): void
    {
        $modpack = Modpack::first();
        $response = $this->putJson('api/modpack/'.$modpack->slug, [
            'name' => 'Updated Name',
        ], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseHas('modpacks', ['id' => $modpack->id, 'name' => 'Updated Name']);
    }

    public function test_update_nonexistent_modpack(): void
    {
        $response = $this->putJson('api/modpack/nonexistent', [
            'name' => 'Test',
        ], $this->writeHeaders());

        $response->assertStatus(404);
    }

    public function test_delete_modpack(): void
    {
        $modpack = Modpack::first();
        $slug = $modpack->slug;

        $response = $this->deleteJson('api/modpack/'.$slug, [], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('modpacks', ['slug' => $slug]);
    }

    // --- Builds CRUD ---

    public function test_create_build(): void
    {
        $modpack = Modpack::first();

        $response = $this->postJson('api/modpack/'.$modpack->slug.'/build', [
            'version' => '2.0.0',
            'minecraft' => '1.20.1',
        ], $this->writeHeaders());

        $response->assertStatus(201);
        $this->assertDatabaseHas('builds', ['modpack_id' => $modpack->id, 'version' => '2.0.0']);
    }

    public function test_create_duplicate_build(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();

        $response = $this->postJson('api/modpack/'.$modpack->slug.'/build', [
            'version' => $build->version,
            'minecraft' => '1.20.1',
        ], $this->writeHeaders());

        $response->assertStatus(422);
    }

    public function test_update_build(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();

        $response = $this->putJson('api/modpack/'.$modpack->slug.'/'.$build->version, [
            'minecraft' => '1.21.0',
        ], $this->writeHeaders());

        $response->assertOk();
    }

    public function test_delete_build(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();
        $version = $build->version;

        $response = $this->deleteJson('api/modpack/'.$modpack->slug.'/'.$version, [], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('builds', ['id' => $build->id]);
    }

    public function test_create_build_with_clone(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();

        $response = $this->postJson('api/modpack/'.$modpack->slug.'/build', [
            'version' => '2.0.0',
            'minecraft' => '1.20.1',
            'clone_from' => $build->version,
        ], $this->writeHeaders());

        $response->assertStatus(201);

        $newBuild = Build::where('version', '2.0.0')->where('modpack_id', $modpack->id)->first();
        $this->assertEquals($build->modversions->count(), $newBuild->modversions->count());
    }

    // --- Build Modversions ---

    public function test_add_mod_to_build(): void
    {
        $modpack = Modpack::first();

        $build = $modpack->builds()->create([
            'version' => '3.0.0',
            'minecraft' => '1.20.1',
        ]);

        $mod = Mod::first();
        $modversion = $mod->versions->first();

        $response = $this->postJson('api/modpack/'.$modpack->slug.'/'.$build->version.'/mod', [
            'mod_slug' => $mod->name,
            'mod_version' => $modversion->version,
        ], $this->writeHeaders());

        $response->assertStatus(201);
    }

    public function test_add_nonexistent_mod_to_build(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();

        $response = $this->postJson('api/modpack/'.$modpack->slug.'/'.$build->version.'/mod', [
            'mod_slug' => 'nonexistent-mod',
            'mod_version' => '1.0.0',
        ], $this->writeHeaders());

        $response->assertStatus(404);
    }

    public function test_remove_mod_from_build(): void
    {
        $modpack = Modpack::first();
        $build = $modpack->builds->first();
        $build->load('modversions.mod');
        $modversion = $build->modversions->first();
        $mod = $modversion->mod;

        $response = $this->deleteJson(
            'api/modpack/'.$modpack->slug.'/'.$build->version.'/mod/'.$mod->name,
            [],
            $this->writeHeaders()
        );

        $response->assertOk();
    }

    // --- Mods CRUD ---

    public function test_create_mod(): void
    {
        $response = $this->postJson('api/mod', [
            'name' => 'new-api-mod',
            'pretty_name' => 'New API Mod',
            'notes' => 'API note',
        ], $this->writeHeaders());

        $response->assertStatus(201);
        $this->assertDatabaseHas('mods', ['name' => 'new-api-mod', 'notes' => 'API note']);
    }

    public function test_create_duplicate_mod(): void
    {
        $mod = Mod::first();
        $response = $this->postJson('api/mod', [
            'name' => $mod->name,
            'pretty_name' => 'Duplicate',
        ], $this->writeHeaders());

        $response->assertStatus(422);
    }

    public function test_update_mod(): void
    {
        $mod = Mod::first();
        $response = $this->putJson('api/mod/'.$mod->name, [
            'pretty_name' => 'Updated Pretty Name',
        ], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseHas('mods', ['id' => $mod->id, 'pretty_name' => 'Updated Pretty Name']);
    }

    public function test_delete_mod(): void
    {
        $mod = Mod::first();
        $name = $mod->name;

        $response = $this->deleteJson('api/mod/'.$name, [], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('mods', ['name' => $name]);
    }

    public function test_mod_notes_excluded_from_read_api(): void
    {
        $mod = Mod::create([
            'name' => 'notes-test-mod',
            'pretty_name' => 'Notes Test Mod',
            'notes' => 'Secret internal note',
        ]);

        config()->set('solder.disable_mod_api', false);

        $response = $this->getJson('api/mod/'.$mod->name);
        $response->assertOk();
        $response->assertJsonMissing(['notes' => 'Secret internal note']);
        $this->assertArrayNotHasKey('notes', $response->json());
    }

    // --- Modversions ---

    public function test_create_modversion(): void
    {
        $mod = Mod::first();
        $response = $this->postJson('api/mod/'.$mod->name.'/version', [
            'version' => '2.0.0',
            'md5' => 'abc123def456',
        ], $this->writeHeaders());

        $response->assertStatus(201);
    }

    public function test_create_duplicate_modversion(): void
    {
        $mod = Mod::first();
        $version = $mod->versions->first();

        $response = $this->postJson('api/mod/'.$mod->name.'/version', [
            'version' => $version->version,
            'md5' => 'abc123',
        ], $this->writeHeaders());

        $response->assertStatus(422);
    }

    public function test_delete_modversion_in_use(): void
    {
        $mod = Mod::first();
        $version = $mod->versions->first();

        $response = $this->deleteJson('api/mod/'.$mod->name.'/'.$version->version, [], $this->writeHeaders());

        $response->assertStatus(409);
        $response->assertJsonFragment(['error' => 'Mod version is in use by 1 build(s) and cannot be deleted.']);
    }

    public function test_delete_modversion(): void
    {
        $mod = Mod::first();
        $version = $mod->versions->first();
        $version->builds()->detach();

        $response = $this->deleteJson('api/mod/'.$mod->name.'/'.$version->version, [], $this->writeHeaders());

        $response->assertOk();
    }

    // --- Tokens ---

    public function test_create_token(): void
    {
        $response = $this->postJson('api/token', [
            'name' => 'NewToken',
        ], $this->writeHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure(['token' => ['id', 'name', 'plaintext', 'created_at']]);
    }

    public function test_list_tokens(): void
    {
        $response = $this->getJson('api/token', $this->writeHeaders());

        $response->assertOk();
        $response->assertJsonStructure(['tokens']);
    }

    public function test_revoke_token(): void
    {
        $user = User::find(1);
        $newToken = $user->createToken('to-revoke');

        $response = $this->deleteJson('api/token/'.$newToken->accessToken->id, [], $this->writeHeaders());

        $response->assertOk();
    }

    // --- Clients ---

    public function test_create_client(): void
    {
        $response = $this->postJson('api/client', [
            'name' => 'API Client',
            'uuid' => 'api-client-uuid-12345',
        ], $this->writeHeaders());

        $response->assertStatus(201);
    }

    public function test_update_client(): void
    {
        $client = Client::first();
        $modpack = Modpack::first();

        $response = $this->putJson('api/client/'.$client->uuid, [
            'modpacks' => [$modpack->id],
        ], $this->writeHeaders());

        $response->assertOk();
    }

    public function test_delete_client(): void
    {
        $client = Client::first();
        $uuid = $client->uuid;

        $response = $this->deleteJson('api/client/'.$uuid, [], $this->writeHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('clients', ['uuid' => $uuid]);
    }

    // --- Read API backward compatibility ---

    public function test_existing_read_endpoints_still_work(): void
    {
        $this->getJson('api/')->assertOk();
        $this->getJson('api/modpack')->assertOk();
    }

    public function test_read_endpoints_with_query_key_still_work(): void
    {
        $key = Key::first();
        $this->getJson('api/modpack?k='.$key->api_key)->assertOk();
    }
}
