<?php

use Illuminate\Database\Migrations\Migration;

class AddImageFields extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table("modpacks", function($table) {
			$table->boolean("icon")->default(0);
			$table->boolean("logo")->default(0);
			$table->boolean("background")->default(0);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table("modpacks", function($table) {
			$table->dropColumn(array("icon", "logo", "background"));
		});
	}

}