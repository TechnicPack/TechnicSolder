<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::table('builds', function ($table) {
            $table->boolean('is_published')->default(0);
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::table('builds', function ($table) {
            $table->dropColumn('is_published');
        });
    }
};
