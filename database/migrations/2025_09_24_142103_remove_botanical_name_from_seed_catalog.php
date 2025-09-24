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
            $table->dropColumn('botanical_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seed_catalog', function (Blueprint $table) {
            $table->string('botanical_name')->after('common_name');
        });
    }
};
