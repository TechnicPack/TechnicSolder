<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewPermissionSystem extends Migration {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_permissions', function($table) {
            $table->dropColumn('solder_create');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('solder_mods');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('solder_modpacks');
        });

        Schema::table('user_permissions', function($table) {  
            $table->boolean('solder_keys')->default(0);
            $table->boolean('solder_clients')->default(0);
            $table->boolean('modpacks_create')->default(0);
            $table->boolean('modpacks_manage')->default(0);
            $table->boolean('modpacks_delete')->default(0);
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_permissions', function($table) {
			$table->dropColumn('modpacks_delete');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('modpacks_manage');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('modpacks_create');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('solder_clients');
        });

        Schema::table('user_permissions', function($table) {
            $table->dropColumn('solder_keys');
        });

        Schema::table('user_permissions', function($table) {
            $table->boolean('solder_modpacks')->default(0);
            $table->boolean('solder_mods')->default(0);
            $table->boolean('solder_create')->default(0);
		});
	}

}
