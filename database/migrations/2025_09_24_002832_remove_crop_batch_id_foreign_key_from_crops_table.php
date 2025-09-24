<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to use a different approach
        // First, let's disable foreign key checks
        DB::statement('PRAGMA foreign_keys=off;');

        // Create a temporary table with the new structure
        Schema::create('crops_new', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tray_number'); // Unique identifier for the tray
            $table->string('tray_type')->default('1020'); // Physical tray type

            // Current status
            $table->foreignId('current_stage_id')->constrained('crop_stages');
            $table->enum('status', [
                'active',       // Currently growing
                'ready',        // Ready for harvest
                'harvested',    // Harvested
                'failed',       // Individual tray failed
                'discarded'     // Tray discarded
            ])->default('active');

            // Stage progression tracking
            $table->timestamp('soak_started_at')->nullable();
            $table->timestamp('germination_started_at')->nullable();
            $table->timestamp('blackout_started_at')->nullable();
            $table->timestamp('light_started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('harvested_at')->nullable();

            // Physical tracking
            $table->string('location')->nullable(); // Shelf A1, Rack B3, etc.
            $table->integer('position_x')->nullable(); // Grid position
            $table->integer('position_y')->nullable(); // Grid position

            // Quality and yield
            $table->integer('germination_rate_percent')->nullable();
            $table->decimal('estimated_yield', 8, 2)->nullable();
            $table->decimal('actual_yield', 8, 2)->nullable();
            $table->string('yield_unit')->default('oz');

            // Quality assessment
            $table->enum('quality_grade', ['A+', 'A', 'B+', 'B', 'C', 'D', 'F'])->nullable();
            $table->json('quality_metrics')->nullable(); // Color, height, density
            $table->text('quality_notes')->nullable();

            // Issue tracking
            $table->json('issues')->nullable(); // Contamination, poor growth, etc.
            $table->boolean('has_contamination')->default(false);
            $table->string('contamination_type')->nullable(); // Mold, bacteria, etc.
            $table->boolean('is_quarantined')->default(false);

            // Environmental exposure
            $table->json('environmental_log')->nullable(); // Temperature, humidity readings
            $table->decimal('total_water_ml')->nullable(); // Water usage tracking

            // Labor tracking
            $table->decimal('labor_hours', 6, 2)->nullable();
            $table->json('labor_log')->nullable(); // Who worked on it, when

            // Harvest details
            $table->foreignId('harvested_by')->nullable()->constrained('users');
            $table->timestamp('packed_at')->nullable();
            $table->string('package_type')->nullable(); // Clamshell, bag, etc.
            $table->string('package_label')->nullable(); // Lot number for packaging

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Batch tracking (string-based, no foreign key)
            $table->string('crop_batch')
                ->comment('Groups trays of same variety planted at same time');
            $table->string('tray_id')
                ->comment('Physical tray identifier (e.g., A1, B2, C3)');

            // Indexes
            $table->index(['uuid']);
            $table->index(['current_stage_id', 'status']);
            $table->index(['tray_number']);
            $table->index(['location']);
            $table->index(['ready_at']);
            $table->index(['harvested_at']);
            $table->index(['has_contamination', 'is_quarantined']);
            $table->index(['quality_grade']);
            $table->index(['harvested_by']);
            $table->index(['crop_batch']);
            $table->index(['crop_batch', 'status']);

            // Unique constraint for tray ID within batch
            $table->unique(['crop_batch', 'tray_id']);
        });

        // Copy any existing data (if there is any)
        DB::statement('INSERT INTO crops_new SELECT
            id, uuid, tray_number, tray_type, current_stage_id, status,
            soak_started_at, germination_started_at, blackout_started_at,
            light_started_at, ready_at, harvested_at, location, position_x, position_y,
            germination_rate_percent, estimated_yield, actual_yield, yield_unit,
            quality_grade, quality_metrics, quality_notes, issues,
            has_contamination, contamination_type, is_quarantined,
            environmental_log, total_water_ml, labor_hours, labor_log,
            harvested_by, packed_at, package_type, package_label, notes,
            created_at, updated_at, deleted_at, crop_batch, tray_id
            FROM crops');

        // Drop the old table
        Schema::dropIfExists('crops');

        // Rename the new table
        DB::statement('ALTER TABLE crops_new RENAME TO crops');

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys=on;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For rollback, we would need to recreate the original table structure
        // This is complex due to the foreign key issues, so we'll keep it simple
        Schema::dropIfExists('crops');
    }
};
