<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->string('icon_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('background_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->dropColumn(['icon_url', 'logo_url', 'background_url']);
        });
    }
};
