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
        Schema::table('builds', function ($table) {
            $table->string('minecraft')->default('');
            $table->string('forge')->nullable();
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('builds', function ($table) {
            $table->dropColumn(['minecraft', 'forge']);
        });
    }
};
