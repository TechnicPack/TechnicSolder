<?php

use Illuminate\Database\Migrations\Migration;

class CreateModTagTable extends Migration {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_tag', function ($table) {
            $table->increments('id');
            $table->integer('mod_id');
            $table->integer('tag_id');
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mod_tag');
    }

}