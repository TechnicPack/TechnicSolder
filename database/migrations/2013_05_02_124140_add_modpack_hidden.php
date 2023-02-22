<?php

use App\Models\Modpack;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->boolean('hidden')->default(1);
        });

        foreach (Modpack::all() as $modpack) {
            $modpack->hidden = false;
            $modpack->save();
        }
    }

    /**
     * Revert the changes to the database.
     */
    public function down(): void
    {
        Schema::table('modpacks', function ($table) {
            $table->dropColumn('hidden');
        });
    }
};
