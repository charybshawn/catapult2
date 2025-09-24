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
        Schema::table('crops', function (Blueprint $table) {
            // Add crop_batch for grouping trays planted together
            $table->string('crop_batch')->after('batch_number')->index()
                ->comment('Groups trays of same variety planted at same time');

            // Add tray_id for individual tray identification
            $table->string('tray_id')->after('crop_batch')
                ->comment('Physical tray identifier (e.g., A1, B2, C3)');

            // Remove or modify tray_count since we're tracking individual trays
            // We'll keep it as it can represent expected trays in the batch

            // Add index for efficient batch queries
            $table->index(['crop_batch', 'current_stage']);
            $table->index(['crop_batch', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropIndex(['crop_batch', 'current_stage']);
            $table->dropIndex(['crop_batch', 'status']);
            $table->dropColumn(['crop_batch', 'tray_id']);
        });
    }
};