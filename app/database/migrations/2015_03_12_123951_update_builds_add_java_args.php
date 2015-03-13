<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBuildsAddJavaArgs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('builds', function($table) {
			$table->string("min_java")->nullable();
			$table->integer("min_memory")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('builds', function($table) {
			$table->dropColumn(array('min_java', 'min_memory'));
		});
	}

}
