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
            $table->dropColumn('solder_modpacks');
            $table->dropColumn('solder_mods');
            $table->dropColumn('solder_create');
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
            $table->dropColumn('modpacks_manage');
            $table->dropColumn('modpacks_create');
            $table->dropColumn('solder_clients');
            $table->dropColumn('solder_keys');
            $table->boolean('solder_modpacks')->default(0);
            $table->boolean('solder_mods')->default(0);
            $table->boolean('solder_create')->default(0);
		});
	}

}
