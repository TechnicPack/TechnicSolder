<?php

class ClientTableTestSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('clients')->delete();

		$testmodpack = Client::create(array('name' => 'TestClient',
							'uuid' => '2ezf6f26-eb15-4ccb-9f0b-8z5ed2c72946',
							));

	}

}
