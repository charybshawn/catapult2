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
        Schema::table('seed_catalog', function (Blueprint $table) {
            // Change cultivar from string to JSON array
            $table->json('cultivars')->after('category');

            // Remove the old single cultivar column
            $table->dropColumn('cultivar');

            // Remove display_name since it will be computed from common_name only
            $table->dropColumn('display_name');

            // Update catalog_id to be based on category + common_name only
            // We'll regenerate these in the model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seed_catalog', function (Blueprint $table) {
            // Restore original structure
            $table->string('cultivar')->after('category');
            $table->string('display_name')->after('category');
            $table->dropColumn('cultivars');
        });
    }
};
