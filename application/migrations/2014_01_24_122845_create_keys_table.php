<?php

class Create_Keys_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('keys', function($table) {
			$table->increments('id');
			$table->string('name');
			$table->string('api_key');
			$table->timestamps();
		});

		$key = new Key();
		$key->name = "default";
		$key->api_key = Config::get('solder.platform_key');
		$key->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('keys');
	}

}