<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('build_modversion', function (Blueprint $table) {
            $table->index('modversion_id');
            $table->index('build_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_modversion', function (Blueprint $table) {
            $table->dropIndex(['modversion_id']);
            $table->dropIndex(['build_id']);
        });
    }
};
