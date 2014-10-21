<?php

use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration {

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