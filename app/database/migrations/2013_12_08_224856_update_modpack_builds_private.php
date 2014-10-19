<?php

use Illuminate\Database\Migrations\Migration;

class UpdateModpackBuildsPrivate extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('modpacks', function($table) {
			$table->boolean('private');
		});

		Schema::table('builds', function($table) {
			$table->boolean('private');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('modpacks', function($table) {
			$table->dropColumn('private');
		});

		Schema::table('builds', function($table) {
			$table->dropColumn('private');
		});
	}

}