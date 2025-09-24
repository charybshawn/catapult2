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
        Schema::create('watering_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('schedule_name');
            $table->text('description')->nullable();

            // Schedule targeting
            $table->string('target_type'); // crop_batch, crop_stage, recipe, location
            $table->unsignedBigInteger('target_id')->nullable(); // ID of the target
            $table->foreignId('recipe_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crop_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location_filter')->nullable(); // Specific growing areas

            // Schedule timing
            $table->enum('frequency_type', [
                'daily',
                'interval_hours',
                'interval_days',
                'specific_times',
                'stage_based',
                'custom'
            ]);
            $table->integer('frequency_value')->nullable(); // Hours or days for intervals
            $table->json('specific_times')->nullable(); // ["08:00", "14:00", "20:00"]
            $table->json('days_of_week')->nullable(); // [1,2,3,4,5] for weekdays

            // Stage-specific scheduling
            $table->json('stage_schedules')->nullable(); // Different schedules by growth stage
            $table->boolean('auto_adjust_by_stage')->default(false);

            // Watering parameters
            $table->decimal('water_amount_ml', 8, 2)->nullable(); // Amount per tray
            $table->enum('watering_method', [
                'bottom_watering',
                'top_watering',
                'misting',
                'drip',
                'flood_and_drain',
                'manual'
            ])->default('bottom_watering');
            $table->integer('duration_seconds')->nullable(); // For automated systems
            $table->decimal('flow_rate_ml_per_min', 8, 2)->nullable();

            // Environmental factors
            $table->json('temperature_adjustments')->nullable(); // Adjust based on temp
            $table->json('humidity_adjustments')->nullable(); // Adjust based on humidity
            $table->json('seasonal_adjustments')->nullable(); // Seasonal modifications

            // Schedule status
            $table->enum('status', ['active', 'inactive', 'seasonal', 'testing'])->default('active');
            $table->date('effective_start_date')->nullable();
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_automatic')->default(false); // For automated irrigation

            // Performance tracking
            $table->decimal('success_rate', 5, 2)->nullable(); // % of successful waterings
            $table->json('performance_metrics')->nullable(); // Water usage, plant health
            $table->timestamp('last_executed_at')->nullable();
            $table->integer('execution_count')->default(0);

            // Notifications
            $table->boolean('send_reminders')->default(true);
            $table->integer('reminder_minutes_before')->default(30);
            $table->json('notification_users')->nullable(); // User IDs to notify

            // Equipment requirements
            $table->json('required_equipment')->nullable(); // Watering tools, irrigation zones
            $table->string('irrigation_zone')->nullable(); // For automated systems
            $table->json('equipment_settings')->nullable(); // Pressure, temperature, etc.

            // Quality control
            $table->boolean('requires_ph_check')->default(false);
            $table->decimal('target_ph_min', 3, 1)->nullable();
            $table->decimal('target_ph_max', 3, 1)->nullable();
            $table->boolean('requires_nutrient_check')->default(false);
            $table->json('nutrient_requirements')->nullable();

            // Override capabilities
            $table->boolean('allow_manual_override')->default(true);
            $table->boolean('require_approval_for_changes')->default(false);
            $table->json('override_log')->nullable(); // History of manual overrides

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['target_type', 'target_id']);
            $table->index(['recipe_id', 'status']);
            $table->index(['crop_stage_id', 'status']);
            $table->index(['status', 'is_automatic']);
            $table->index(['effective_start_date', 'effective_end_date']);
            $table->index(['frequency_type', 'status']);
            $table->index(['location_filter']);
            $table->index(['last_executed_at']);
            $table->index(['irrigation_zone']);
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watering_schedules');
    }
};