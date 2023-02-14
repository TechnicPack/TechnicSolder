<?php

namespace Database\Seeders;

use App\Models\Key;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeyTableTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('keys')->truncate();

        $testmodpack = Key::create([
            'name' => 'TestKey',
            'api_key' => 'sfIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ',
        ]);
    }
}
