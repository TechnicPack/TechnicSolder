<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class SolderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::find(1);
        $this->be($user);
    }

    public function test_configure_page(): void
    {
        $response = $this->get('/solder/configure');

        $response->assertOk();
        $response->assertSee('Configure Solder');
    }

    public function test_update_page(): void
    {
        // Pre-seed the cache so UpdateUtils doesn't hit GitHub
        Cache::put('update:github:changelog:main', [
            [
                'sha' => 'abc123',
                'html_url' => 'https://example.com',
                'commit' => [
                    'message' => 'test commit',
                    'author' => ['date' => '2025-01-01T00:00:00Z', 'name' => 'Test'],
                ],
                'author' => ['avatar_url' => 'https://example.com/avatar.png', 'login' => 'test'],
                'committer' => ['avatar_url' => 'https://example.com/avatar.png', 'login' => 'test'],
            ],
        ], now()->addHour());

        Cache::put('update:github:tags', [
            ['name' => '0.0.1'],
        ], now()->addHour());

        $response = $this->get('/solder/update');

        $response->assertOk();
        $response->assertSee('Update Manager');
    }
}
