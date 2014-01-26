<?php

class Create_Donor_Field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('modpacks', function($table) {
			$table->boolean('donor_only')->default(0);
			$table->float('donor_threshold')->default(0);
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
			$table->drop_column(array('donor_only', 'donor_threshold'));
		});
	}

}