<?php

use Illuminate\Database\Migrations\Migration;

class UpdateBuildsTable extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('builds', function($table) {
			$table->string('minecraft')->default('');
			$table->string('forge')->nullable();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('builds', function($table) {
			$table->dropColumn(array('minecraft','forge'));
		});
	}

}