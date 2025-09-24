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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('crop_batch_id')->constrained()->cascadeOnDelete();
            $table->string('tray_number')->index(); // Unique identifier for the tray
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
            $table->enum('quality_grade', ['A', 'B', 'C', 'D', 'F'])->nullable();
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

            // Indexes
            $table->index(['crop_batch_id', 'status']);
            $table->index(['current_stage_id', 'status']);
            $table->index(['tray_number', 'crop_batch_id']);
            $table->index(['location']);
            $table->index(['ready_at']);
            $table->index(['harvested_at']);
            $table->index(['has_contamination', 'is_quarantined']);
            $table->index(['quality_grade']);
            $table->index(['harvested_by']);

            // Unique constraint for tray number within batch
            $table->unique(['crop_batch_id', 'tray_number']);

            // Spatial index for position if using PostgreSQL
            // $table->index(['position_x', 'position_y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};