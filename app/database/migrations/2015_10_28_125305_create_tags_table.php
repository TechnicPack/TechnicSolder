<?php

use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('pretty_name');
            $table->timestamps();
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tags');
    }

}