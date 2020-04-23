<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration {

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
			$table->string('created_ip');
			$table->string('last_ip')->nullable();
			$table->nullableTimestamps();
		});

		/**
		 * Create default user (if one doesn't exist)
		 **/
        if (User::count() == 0) {
            $user = new User();
            $user->username = 'admin';
            $user->email = 'admin@admin.com';
            $user->password = Hash::make('admin');
            $user->created_ip = getHostByName(getHostName());
            $user->save();
        }
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