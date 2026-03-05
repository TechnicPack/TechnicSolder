<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('user_permissions', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('solder_full')->default(0);
            $table->boolean('solder_users')->default(0);
            $table->boolean('solder_modpacks')->default(0);
            $table->boolean('solder_mods')->default(0);
            $table->boolean('solder_create')->default(0);
            $table->boolean('mods_create')->default(0);
            $table->boolean('mods_manage')->default(0);
            $table->boolean('mods_delete')->default(0);
            $table->string('modpacks')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('user_permissions');
    }
};
