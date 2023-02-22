<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('mods', function ($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('link')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('mods');
    }
};
