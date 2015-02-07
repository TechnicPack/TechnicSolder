<?php

class ModTableTestSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('mods')->delete();

		$testmod = Mod::create(array('pretty_name' => 'TestMod',
							'name' => 'testmod',
							'description' => 'This is a test mod for Solder',
							'author' => 'Technic',
							'link' => 'http://solder.io'
							));

		DB::table('modversions')->delete();

		$testmodversion = Modversion::create(array('mod_id' => $testmod->id,
							'version' => '0.1',
							'md5' => 'fb6582e4d9c9bc208181907ecc108eb1'
							));

	}

}
