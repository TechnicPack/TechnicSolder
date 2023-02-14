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
        Schema::create('modversions', function ($table) {
            $table->increments('id');
            $table->integer('mod_id');
            $table->string('version');
            $table->string('md5');
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
        Schema::drop('modversions');
    }
};
