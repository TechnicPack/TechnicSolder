<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('modpacks', function ($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('recommended')->nullable();
            $table->string('latest')->nullable();
            $table->string('url')->nullable();
            $table->string('icon_md5')->nullable();
            $table->string('logo_md5')->nullable();
            $table->string('background_md5')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('modpacks');
    }
};
