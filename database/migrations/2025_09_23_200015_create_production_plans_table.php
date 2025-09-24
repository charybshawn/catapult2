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
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('plan_name');
            $table->text('description')->nullable();

            // Planning period
            $table->date('plan_start_date');
            $table->date('plan_end_date');
            $table->enum('plan_type', [
                'weekly',
                'monthly',
                'seasonal',
                'annual',
                'custom'
            ])->default('weekly');

            // Status tracking
            $table->enum('status', [
                'draft',        // Being planned
                'approved',     // Approved for execution
                'active',       // Currently executing
                'completed',    // Finished
                'cancelled',    // Cancelled
                'on_hold'       // Temporarily paused
            ])->default('draft');

            // Capacity planning
            $table->integer('total_tray_capacity');
            $table->integer('planned_trays');
            $table->integer('buffer_trays')->default(0); // Extra capacity for failures
            $table->decimal('capacity_utilization', 5, 2)->storedAs('(planned_trays * 100.0) / total_tray_capacity');

            // Financial projections
            $table->decimal('projected_revenue', 12, 2)->nullable();
            $table->decimal('projected_costs', 12, 2)->nullable();
            $table->decimal('projected_profit', 12, 2)->nullable();
            $table->decimal('projected_margin', 5, 2)->nullable(); // Profit margin %

            // Resource requirements
            $table->json('seed_requirements')->nullable(); // Variety: quantity needed
            $table->json('labor_requirements')->nullable(); // Hours by week/period
            $table->json('supply_requirements')->nullable(); // Growing media, trays, etc.

            // Customer demand
            $table->json('customer_demand')->nullable(); // Customer: projected orders
            $table->decimal('demand_fulfillment_rate', 5, 2)->nullable(); // % of demand planned

            // Production scheduling
            $table->json('planting_schedule')->nullable(); // Week: varieties to plant
            $table->json('harvest_schedule')->nullable(); // Week: varieties to harvest
            $table->json('delivery_schedule')->nullable(); // Week: deliveries planned

            // Risk management
            $table->decimal('failure_buffer_percent', 5, 2)->default(10); // % extra to plant
            $table->json('risk_factors')->nullable(); // Identified risks
            $table->json('contingency_plans')->nullable(); // Backup plans

            // Performance tracking
            $table->integer('batches_planned')->default(0);
            $table->integer('batches_created')->default(0);
            $table->integer('batches_completed')->default(0);
            $table->decimal('plan_execution_rate', 5, 2)->nullable(); // % of plan executed

            // Actual vs planned
            $table->decimal('actual_revenue', 12, 2)->nullable();
            $table->decimal('actual_costs', 12, 2)->nullable();
            $table->decimal('actual_profit', 12, 2)->nullable();
            $table->integer('actual_trays_used')->nullable();

            // Environmental factors
            $table->json('seasonal_adjustments')->nullable(); // Seasonal growth variations
            $table->json('weather_considerations')->nullable();

            // Team assignments
            $table->foreignId('plan_manager_id')->constrained('users');
            $table->json('assigned_growers')->nullable(); // User IDs of assigned growers
            $table->json('assigned_harvesters')->nullable();

            // Version control
            $table->string('version', 10)->default('1.0');
            $table->foreignId('parent_plan_id')->nullable()->constrained('production_plans');
            $table->text('revision_notes')->nullable();

            // Approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'plan_start_date']);
            $table->index(['plan_type', 'status']);
            $table->index(['plan_start_date', 'plan_end_date']);
            $table->index(['plan_manager_id', 'status']);
            $table->index(['approved_by', 'approved_at']);
            $table->index(['created_by', 'plan_start_date']);
            $table->index(['capacity_utilization']);
            $table->index(['plan_execution_rate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_plans');
    }
};