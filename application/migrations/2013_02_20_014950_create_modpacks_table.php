<?php

class Create_Modpacks_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modpacks', function($table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('slug')->unique();
			$table->string('recommended');
			$table->string('latest');
			$table->string('url')->nullable();
			$table->string('icon_md5');
			$table->string('logo_md5');
			$table->string('background_md5');
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
		Schema::drop('modpacks');
	}

}