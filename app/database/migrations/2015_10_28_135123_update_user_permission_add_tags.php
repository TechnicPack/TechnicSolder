<?php

use Illuminate\Database\Migrations\Migration;

class UpdateUserPermissionAddTags extends Migration {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up() {
        Schema::table(
            'user_permissions',
            function ($table) {
                $table->boolean('tags_create')->default(0);
                $table->boolean('tags_manage')->default(0);
                $table->boolean('tags_delete')->default(0);
            }
        );
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down() {
        Schema::table(
            'user_permissions',
            function ($table) {
                $table->dropColumn(
                    array(
                        'tags_delete',
                        'tags_manage',
                        'tags_create',
                    )
                );
            }
        );
    }

}
