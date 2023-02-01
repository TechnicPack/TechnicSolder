<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->boolean('icon')->default(0);
            $table->boolean('logo')->default(0);
            $table->boolean('background')->default(0);
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->dropColumn(['icon', 'logo', 'background']);
        });
    }
};
