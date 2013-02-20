<?php

class Create_Builds_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('builds', function($table) {
			$table->increments('id');
			$table->integer('modpack_id');
			$table->string('version');
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
		Schema::drop('builds');
	}

}