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
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('harvest_number')->unique();
            $table->foreignId('crop_batch_id')->constrained()->cascadeOnDelete();
            $table->date('harvest_date');
            $table->time('harvest_time');

            // Harvest details
            $table->integer('trays_harvested');
            $table->decimal('total_yield', 10, 2); // Total weight harvested
            $table->string('yield_unit')->default('oz');
            $table->decimal('average_yield_per_tray', 8, 2);

            // Quality assessment
            $table->enum('overall_quality', ['A', 'B', 'C', 'D', 'F']);
            $table->json('quality_breakdown')->nullable(); // Percentage by grade
            $table->integer('customer_grade_percent')->nullable(); // % meeting customer standards
            $table->text('quality_notes')->nullable();

            // Waste tracking
            $table->decimal('waste_amount', 10, 2)->nullable();
            $table->string('waste_unit')->default('oz');
            $table->json('waste_reasons')->nullable(); // Contamination, overripe, etc.

            // Processing details
            $table->foreignId('harvested_by')->constrained('users');
            $table->json('harvest_team')->nullable(); // Additional team members
            $table->decimal('harvest_duration_hours', 5, 2)->nullable();
            $table->decimal('processing_duration_hours', 5, 2)->nullable();

            // Post-harvest processing
            $table->timestamp('washing_completed_at')->nullable();
            $table->timestamp('packaging_completed_at')->nullable();
            $table->timestamp('cold_storage_at')->nullable();
            $table->decimal('storage_temperature', 4, 1)->nullable();

            // Lot tracking for food safety
            $table->string('lot_number')->unique();
            $table->json('seed_lot_numbers')->nullable(); // Source seed lots
            $table->date('best_by_date')->nullable();
            $table->integer('shelf_life_days')->nullable();

            // Packaging details
            $table->json('packaging_breakdown')->nullable(); // Containers by type/size
            $table->integer('total_packages')->nullable();
            $table->json('package_weights')->nullable(); // Individual package weights

            // Cost analysis
            $table->decimal('total_production_cost', 10, 2)->nullable();
            $table->decimal('cost_per_unit', 10, 4)->nullable();
            $table->decimal('labor_cost', 10, 2)->nullable();
            $table->decimal('material_cost', 10, 2)->nullable();

            // Sales allocation
            $table->enum('allocation_status', ['available', 'reserved', 'sold', 'discarded'])->default('available');
            $table->decimal('allocated_amount', 10, 2)->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable();

            // Environmental conditions during harvest
            $table->json('environmental_conditions')->nullable(); // Temp, humidity during harvest
            $table->text('harvest_conditions_notes')->nullable();

            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // Photo URLs
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['crop_batch_id', 'harvest_date']);
            $table->index(['harvest_date', 'allocation_status']);
            $table->index(['lot_number']);
            $table->index(['best_by_date']);
            $table->index(['overall_quality']);
            $table->index(['harvested_by', 'harvest_date']);
            $table->index(['allocation_status', 'remaining_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};