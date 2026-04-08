<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('link');
        });

        Schema::table('modversions', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('filesize');
        });
    }

    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('modversions', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
