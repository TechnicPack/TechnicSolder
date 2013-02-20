<?php

class Create_Mods_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mods', function($table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->text('description');
			$table->string('author');
			$table->string('link');
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
		Schema::drop('mods');
	}

}