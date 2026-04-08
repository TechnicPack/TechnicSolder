<?php

namespace Tests\Feature;

use App\Models\Mod;
use App\Models\Modversion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_dashboard_shows_unused_mod_versions(): void
    {
        $mod = Mod::create([
            'pretty_name' => 'UnusedMod',
            'name' => 'unusedmod',
        ]);

        Modversion::create([
            'mod_id' => $mod->id,
            'version' => '2.0',
            'md5' => 'abc123def456abc123def456abc123de',
            'filesize' => '1024',
        ]);

        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Unused Mod Versions');
        $response->assertSee('UnusedMod');
    }

    public function test_dashboard_shows_empty_state_when_no_unused_mod_versions(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Unused Mod Versions');
        $response->assertSee('No unused mod versions found.');
    }
}
