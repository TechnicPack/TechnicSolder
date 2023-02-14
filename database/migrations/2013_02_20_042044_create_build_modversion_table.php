<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
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
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('build_modversion');
    }
};
