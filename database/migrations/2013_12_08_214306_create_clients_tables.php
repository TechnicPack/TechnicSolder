<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::create('clients', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('uuid');
            $table->nullableTimestamps();
        });

        Schema::create('client_modpack', function ($table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('modpack_id');
            $table->nullableTimestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::drop('clients');
        Schema::drop('client_modpack');
    }
};
