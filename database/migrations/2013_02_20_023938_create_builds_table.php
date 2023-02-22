<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('builds', function ($table) {
            $table->increments('id');
            $table->integer('modpack_id');
            $table->string('version');
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('builds');
    }
};
