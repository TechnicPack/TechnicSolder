<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTableSecurity extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table("users", function($table) {
			$table->string('updated_by_ip')->nullable();
			$table->integer('created_by_user_id')->default(1);
			$table->integer('updated_by_user_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table("users", function($table) {
			$table->dropColumn(array('created_by_user_id', 'updated_by_ip', 'updated_by_user_id'));
		});
	}

}
