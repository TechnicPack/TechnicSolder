<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('build_modversion', function ($table) {
            $table->increments('id');
            $table->integer('modversion_id');
            $table->integer('build_id');
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('build_modversion');
    }
};
