<?php

class ModpackTableTestSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('modpacks')->delete();

		$testmodpack = Modpack::create(array('name' => 'TestModpack',
							'slug' => 'testmodpack',
							'icon' => false,
							'icon_md5' => md5_file(public_path() . '/resources/default/icon.png'),
							'icon_url' => URL::asset('/resources/default/icon.png'),
							'logo' => false,
							'logo_md5' => md5_file(public_path() . '/resources/default/logo.png'),
							'logo_url' => URL::asset('/resources/default/logo.png'),
							'background' => false,
							'background_md5' => md5_file(public_path() . '/resources/default/background.jpg'),
							'background_url' => URL::asset('/resources/default/background.jpg')
							));

		DB::table('builds')->delete();

		$testbuild = Build::create(array('modpack_id' => $testmodpack->id,
							'version' => '1.0.0',
							'minecraft' => '1.7.10',
							'min_java' => '1.7',
							'min_memory' => '1024',
							'is_published' => true
							));

	}

}
