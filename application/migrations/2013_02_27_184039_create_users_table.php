<?php

class Create_Users_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
			$table->increments('id');
			$table->string('username');
			$table->string('email');
			$table->string('password');
			$table->string('created_ip')->default('0.0.0.0');
			$table->string('last_ip')->nullable();
			$table->timestamps();
		});

		/**
		 * Create Default User
		 **/
		$user = new User();
		$user->username = 'admin';
		$user->email = 'admin@admin.com';
		$user->password = Hash::make('admin');
		$user->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}