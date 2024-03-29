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
            $table->string('min_java')->nullable();
            $table->integer('min_memory')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('builds', function ($table) {
            $table->dropColumn(['min_java', 'min_memory']);
        });
    }
};
