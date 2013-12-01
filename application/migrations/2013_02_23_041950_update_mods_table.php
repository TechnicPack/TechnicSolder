<?php

class Update_Mods_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mods', function($table) {
			$table->string('pretty_name');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mods', function($table) {
			$table->drop_column('pretty_name');
		});
	}

}