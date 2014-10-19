<?php

use Illuminate\Database\Migrations\Migration;

class CreateModversionsTable extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modversions', function ($table) {
			$table->increments('id');
			$table->integer('mod_id');
			$table->string('version');
			$table->string('md5');
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
		Schema::drop('modversions');
	}

}