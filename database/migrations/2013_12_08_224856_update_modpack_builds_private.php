<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->boolean('private')->default(0);
        });

        Schema::table('builds', function ($table) {
            $table->boolean('private')->default(0);
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->dropColumn('private');
        });

        Schema::table('builds', function ($table) {
            $table->dropColumn('private');
        });
    }
};
