<?php

class Update_Builds_Table_Again {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('builds', function($table) {
			$table->boolean('is_published')->default(0);
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
			$table->drop_column('is_published');
		});
	}

}