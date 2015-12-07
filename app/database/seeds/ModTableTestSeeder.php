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
							'version' => '1.0',
							'md5' => 'bdbc6c6cc48c7b037e4aef64b58258a3',
							'filesize' => '295'
							));

	}

}
