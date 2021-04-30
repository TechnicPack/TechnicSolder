<?php

use App\Key;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeyTableTestSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('keys')->delete();

        $testmodpack = Key::create([
            'name' => 'TestKey',
            'api_key' => 'sfIvEcNueZtwKsTAIYOIYng1iuPAgavJsfIvEcNueZtwKsTAIYOIYng1iuPAgavJ',
        ]);

    }

}
