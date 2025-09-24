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
        Schema::create('crop_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('batch_number')->unique();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_lot_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('tray_count');
            $table->string('tray_range')->nullable(); // e.g., "101-150"

            // Planning dates
            $table->date('planned_plant_date');
            $table->date('planned_harvest_date');

            // Actual dates
            $table->date('actual_plant_date')->nullable();
            $table->timestamp('soak_started_at')->nullable();
            $table->timestamp('germination_started_at')->nullable();
            $table->timestamp('blackout_started_at')->nullable();
            $table->timestamp('light_started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Current status
            $table->foreignId('current_stage_id')->constrained('crop_stages');
            $table->enum('status', [
                'planned',      // Scheduled but not started
                'active',       // Currently growing
                'ready',        // Ready for harvest
                'harvested',    // Fully harvested
                'failed',       // Crop failed
                'cancelled'     // Batch cancelled
            ])->default('planned');

            // Resource allocation
            $table->decimal('total_seeds_used', 10, 4)->nullable();
            $table->string('growing_location')->nullable(); // Rack A, Room 1, etc.
            $table->json('environmental_conditions')->nullable(); // Temp, humidity logs

            // Quality tracking
            $table->integer('germination_rate_percent')->nullable();
            $table->json('quality_notes')->nullable(); // Color, growth rate, issues
            $table->text('issues_encountered')->nullable();

            // Production metrics
            $table->decimal('expected_yield', 10, 2)->nullable();
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->decimal('waste_amount', 10, 2)->nullable();
            $table->text('waste_reason')->nullable();

            // Labor tracking
            $table->decimal('total_labor_hours', 8, 2)->nullable();
            $table->decimal('total_labor_cost', 10, 2)->nullable();

            // Customer allocation
            $table->foreignId('reserved_for_customer_id')->nullable()->constrained('customers');
            $table->foreignId('reserved_for_order_id')->nullable()->constrained('orders');

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'current_stage_id']);
            $table->index(['recipe_id', 'status']);
            $table->index(['planned_plant_date', 'status']);
            $table->index(['planned_harvest_date', 'status']);
            $table->index(['actual_plant_date']);
            $table->index(['ready_at']);
            $table->index(['growing_location']);
            $table->index(['reserved_for_customer_id']);
            $table->index(['reserved_for_order_id']);
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_batches');
    }
};