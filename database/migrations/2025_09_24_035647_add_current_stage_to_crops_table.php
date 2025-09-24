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
            $table->string('current_stage')->nullable()->after('current_stage_id');
            $table->datetime('stage_started_at')->nullable()->after('current_stage');
        });

        // Set default values for existing records
        DB::table('crops')->update([
            'current_stage' => 'soaking',
            'stage_started_at' => DB::raw('soak_started_at')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn(['current_stage', 'stage_started_at']);
        });
    }
};
