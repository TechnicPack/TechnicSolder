<?php

class Create_Clients_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("clients", function($table) {
			$table->increments('id');
			$table->string('name');
			$table->string('uuid');
			$table->timestamps();
		});

		Schema::create("client_modpack", function($table) {
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('modpack_id');
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
		Schema::drop('clients');
		Schema::drop('client_modpack');
	}

}