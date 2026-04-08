<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modversions', function (Blueprint $table) {
            $table->index('mod_id');
        });

        Schema::table('builds', function (Blueprint $table) {
            $table->index('modpack_id');
        });

        Schema::table('user_permissions', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('client_modpack', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('modpack_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->index('uuid');
        });

        Schema::table('keys', function (Blueprint $table) {
            $table->index('api_key');
        });
    }

    public function down(): void
    {
        Schema::table('modversions', function (Blueprint $table) {
            $table->dropIndex(['mod_id']);
        });

        Schema::table('builds', function (Blueprint $table) {
            $table->dropIndex(['modpack_id']);
        });

        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('client_modpack', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['modpack_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['uuid']);
        });

        Schema::table('keys', function (Blueprint $table) {
            $table->dropIndex(['api_key']);
        });
    }
};
