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
        Schema::table('mods', function ($table) {
            $table->string('pretty_name')->default('');
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mods', function ($table) {
            $table->dropColumn('pretty_name');
        });
    }
};
