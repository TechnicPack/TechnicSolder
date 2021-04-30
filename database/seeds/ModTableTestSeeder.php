<?php

use App\Mod;
use App\Modversion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModTableTestSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mods')->delete();

        $testmod = Mod::create([
            'pretty_name' => 'TestMod',
            'name' => 'testmod',
            'description' => 'This is a test mod for Solder',
            'author' => 'Technic',
            'link' => 'http://solder.io'
        ]);

        DB::table('modversions')->delete();

        $testmodversion = Modversion::create([
            'mod_id' => $testmod->id,
            'version' => '1.0',
            'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
            'filesize' => '295'
        ]);

        Mod::create([
            'pretty_name' => 'Backtools',
            'name' => 'backtools',
            'link' => 'http://ichun.us',
        ]);

    }

}
