<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('builds', function ($table) {
            $table->dropColumn('minecraft_md5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('builds', function ($table) {
            $table->string('minecraft_md5')->default('');
        });
    }
};
