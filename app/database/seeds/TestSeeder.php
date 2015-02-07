<?php

class TestSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		//$this->call('UserTableSeeder');
		$this->call('ModpackTableTestSeeder');
		$this->call('ModTableTestSeeder');
		$this->call('ClientTableTestSeeder');
		$this->call('KeyTableTestSeeder');

		DB::table('build_modversion')->delete();

		$testbuild = Build::find(1);

		//Add testmodversion to testbuild
		$testbuild->modversions()->attach(1);
	}

}
