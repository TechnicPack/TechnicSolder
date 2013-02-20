<?php

class Create_Build_Modversion_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('build_modversion', function ($table) {
			$table->increments('id');
			$table->integer('modversion_id');
			$table->integer('build_id');
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
		Schema::drop('build_modversion');
	}

}