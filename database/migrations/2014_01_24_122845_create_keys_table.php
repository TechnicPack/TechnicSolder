<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('keys', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('api_key');
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('keys');
    }
};
