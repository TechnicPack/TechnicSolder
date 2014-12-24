<?php

use Illuminate\Database\Migrations\Migration;

class CreateModpacksTable extends Migration {

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
			$table->string('recommended')->nullable();
			$table->string('latest')->nullable();
			$table->string('url')->nullable();
			$table->string('icon_md5')->nullable();
			$table->string('logo_md5')->nullable();
			$table->string('background_md5')->nullable();
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