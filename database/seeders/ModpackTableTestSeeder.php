<?php

namespace Database\Seeders;

use App\Models\Build;
use App\Models\Modpack;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ModpackTableTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modpacks')->truncate();

        $testmodpack = Modpack::create([
            'name' => 'TestModpack',
            'slug' => 'testmodpack',
            'icon' => false,
            'icon_md5' => null,
            'icon_url' => URL::asset('/resources/default/icon.png'),
            'logo' => false,
            'logo_md5' => null,
            'logo_url' => URL::asset('/resources/default/logo.png'),
            'background' => false,
            'background_md5' => null,
            'background_url' => URL::asset('/resources/default/background.jpg'),
        ]);

        DB::table('builds')->truncate();

        $testbuild = Build::create([
            'modpack_id' => $testmodpack->id,
            'version' => '1.0.0',
            'minecraft' => '1.7.10',
            'min_java' => '1.7',
            'min_memory' => '1024',
            'is_published' => true,
        ]);
    }
}
