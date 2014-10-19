<?php

use Illuminate\Database\Migrations\Migration;

class AddModpackHidden extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('modpacks', function($table) {
			$table->boolean('hidden')->default(1);
		});

		foreach (Modpack::all() as $modpack) {
			$modpack->hidden = false;
			$modpack->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('modpacks', function($table) {
			$table->dropColumn('hidden');
		});
	}

}