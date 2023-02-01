<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modversions', function ($table) {
            $table->integer('filesize')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modversions', function ($table) {
            $table->dropColumn('filesize');
        });
    }
};
