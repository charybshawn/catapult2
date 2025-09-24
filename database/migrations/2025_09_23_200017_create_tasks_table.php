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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();

            // Task classification
            $table->enum('task_type', [
                'watering',
                'stage_advancement',
                'harvest',
                'planting',
                'quality_check',
                'cleaning',
                'maintenance',
                'inventory_count',
                'packaging',
                'delivery',
                'administrative',
                'other'
            ]);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Task targeting - what this task is for
            $table->string('target_type')->nullable(); // crop_batch, crop, order, location
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('location')->nullable(); // Specific area, rack, room

            // Scheduling
            $table->datetime('scheduled_at');
            $table->datetime('due_at')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', [
                'daily', 'weekly', 'monthly', 'custom'
            ])->nullable();
            $table->json('recurring_schedule')->nullable(); // Cron-like or specific days

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->datetime('assigned_at');
            $table->json('required_skills')->nullable(); // Skills/roles needed
            $table->enum('assignment_method', [
                'manual', 'automatic', 'self_assigned'
            ])->default('manual');

            // Status tracking
            $table->enum('status', [
                'pending',      // Scheduled but not started
                'assigned',     // Assigned to someone
                'in_progress',  // Being worked on
                'completed',    // Finished successfully
                'skipped',      // Intentionally skipped
                'failed',       // Could not complete
                'cancelled',    // Cancelled
                'overdue'       // Past due date
            ])->default('pending');

            // Execution tracking
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->integer('actual_duration_minutes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('completion_data')->nullable(); // Task-specific results
            $table->foreignId('completed_by')->nullable()->constrained('users');

            // Quality and verification
            $table->boolean('requires_verification')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->datetime('verified_at')->nullable();
            $table->enum('verification_status', [
                'pending', 'approved', 'rejected', 'needs_rework'
            ])->nullable();
            $table->text('verification_notes')->nullable();

            // Dependencies
            $table->json('depends_on_tasks')->nullable(); // Task IDs this depends on
            $table->json('blocks_tasks')->nullable(); // Task IDs that depend on this
            $table->boolean('dependency_met')->default(true);

            // Resources required
            $table->json('required_tools')->nullable(); // Tools needed
            $table->json('required_supplies')->nullable(); // Consumables needed
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->decimal('actual_cost', 8, 2)->nullable();

            // Automation integration
            $table->boolean('can_auto_complete')->default(false);
            $table->json('automation_config')->nullable(); // Settings for auto-completion
            $table->string('automation_trigger')->nullable(); // What triggers auto-completion

            // Notifications
            $table->boolean('send_notifications')->default(true);
            $table->json('notification_settings')->nullable(); // When and how to notify
            $table->datetime('last_notification_sent')->nullable();
            $table->integer('notification_count')->default(0);

            // Performance metrics
            $table->decimal('efficiency_score', 5, 2)->nullable(); // Performance rating
            $table->boolean('was_on_time')->nullable();
            $table->integer('minutes_variance')->nullable(); // Actual vs estimated time

            // Template information
            $table->foreignId('task_template_id')->nullable(); // If created from template
            $table->json('template_overrides')->nullable(); // Modified template values

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'scheduled_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['task_type', 'status']);
            $table->index(['priority', 'due_at']);
            $table->index(['target_type', 'target_id']);
            $table->index(['location', 'scheduled_at']);
            $table->index(['is_recurring', 'status']);
            $table->index(['due_at', 'status']);
            $table->index(['completed_at']);
            $table->index(['assigned_by', 'assigned_at']);
            $table->index(['dependency_met', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};