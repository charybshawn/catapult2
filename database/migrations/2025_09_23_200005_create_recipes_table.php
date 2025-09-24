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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('variety'); // Sunflower, Pea, Radish, etc.
            $table->text('description')->nullable();
            $table->foreignId('seed_consumable_id')->constrained('consumables');
            $table->decimal('seed_density_per_tray', 8, 2); // grams per tray
            $table->string('tray_type')->default('1020'); // 1020, 1010, etc.

            // Growth stages with days
            $table->integer('soak_hours')->default(0);
            $table->integer('germination_days')->default(2);
            $table->integer('blackout_days')->default(3);
            $table->integer('light_days')->default(4);
            $table->integer('total_days')->storedAs('germination_days + blackout_days + light_days');

            // Environmental parameters
            $table->json('temperature_ranges')->nullable(); // Min/max by stage
            $table->json('humidity_ranges')->nullable(); // Min/max by stage
            $table->json('watering_schedule')->nullable(); // Frequency by stage

            // Yield expectations
            $table->decimal('expected_yield_grams', 8, 2)->nullable();
            $table->decimal('minimum_acceptable_yield', 8, 2)->nullable();
            $table->decimal('cost_per_tray', 8, 2)->nullable(); // Seeds + supplies
            $table->decimal('selling_price_per_unit', 8, 2)->nullable();
            $table->string('selling_unit')->default('oz'); // oz, lbs, grams

            // Quality parameters
            $table->json('quality_metrics')->nullable(); // Color, texture, taste notes
            $table->json('harvest_specifications')->nullable(); // Height, appearance

            // Recipe status and versioning
            $table->string('version', 10)->default('1.0');
            $table->foreignId('parent_recipe_id')->nullable()->constrained('recipes');
            $table->enum('status', ['draft', 'active', 'archived', 'testing'])->default('draft');
            $table->boolean('is_organic')->default(false);
            $table->boolean('is_specialty')->default(false); // Custom blends, etc.

            // Success tracking
            $table->decimal('success_rate', 5, 2)->nullable(); // % of successful batches
            $table->integer('times_used')->default(0);
            $table->timestamp('last_used_at')->nullable();

            $table->text('notes')->nullable();
            $table->json('growing_tips')->nullable(); // Troubleshooting, best practices
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['variety', 'status']);
            $table->index(['seed_consumable_id', 'status']);
            $table->index(['status', 'is_organic']);
            $table->index(['total_days']);
            $table->index(['success_rate']);
            $table->index(['last_used_at']);
            $table->index(['created_by']);

            // Unique constraint for name and version
            $table->unique(['name', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};