<?php

namespace Database\Seeders;

use App\Build;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call('UserTableSeeder');
        $this->call(ModpackTableTestSeeder::class);
        $this->call(ModTableTestSeeder::class);
        $this->call(ClientTableTestSeeder::class);
        $this->call(KeyTableTestSeeder::class);

        DB::table('build_modversion')->truncate();

        $testbuild = Build::find(1);

        //Add testmodversion to testbuild
        $testbuild->modversions()->attach(1);
    }
}
