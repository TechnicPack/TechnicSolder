<?php

class Update_Modpack_Builds_Private {

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
			$table->drop_column('private');
		});

		Schema::table('builds', function($table) {
			$table->drop_column('private');
		});
	}

}