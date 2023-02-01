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
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clients');
        Schema::drop('client_modpack');
    }
};
