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
        Schema::create('time_clock_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Clock in/out details
            $table->enum('entry_type', ['clock_in', 'clock_out', 'break_start', 'break_end']);
            $table->datetime('timestamp');
            $table->date('work_date'); // Date this entry applies to (may differ from timestamp)

            // Location and device tracking
            $table->string('clock_location')->nullable(); // Physical location or station
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info')->nullable(); // Browser, mobile app, kiosk
            $table->json('gps_coordinates')->nullable(); // For mobile clock-ins
            $table->boolean('is_mobile_entry')->default(false);

            // Work context
            $table->enum('work_type', [
                'production',       // Growing, harvesting
                'packaging',        // Post-harvest processing
                'delivery',         // Customer deliveries
                'maintenance',      // Equipment, facility
                'administrative',   // Office work
                'cleaning',         // Sanitization, housekeeping
                'training',         // Learning, onboarding
                'meeting',          // Team meetings
                'other'
            ])->nullable();

            // Task/project association
            $table->foreignId('active_task_id')->nullable()->constrained('tasks');
            $table->string('project_code')->nullable(); // Cost center, department
            $table->string('activity_description')->nullable();

            // Break tracking
            $table->enum('break_type', [
                'lunch', 'rest', 'personal', 'meeting', 'training'
            ])->nullable();
            $table->boolean('is_paid_break')->nullable();

            // Validation and approval
            $table->enum('status', [
                'pending',      // Awaiting review
                'approved',     // Approved
                'rejected',     // Rejected
                'disputed',     // Employee disputes rejection
                'auto_approved' // Automatically approved
            ])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->datetime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('dispute_reason')->nullable();

            // Time calculations (computed from paired entries)
            $table->decimal('hours_worked', 8, 4)->nullable();
            $table->decimal('break_hours', 8, 4)->nullable();
            $table->decimal('overtime_hours', 8, 4)->nullable();
            $table->decimal('regular_hours', 8, 4)->nullable();

            // Adjustments
            $table->boolean('is_manual_entry')->default(false);
            $table->foreignId('manual_entry_by')->nullable()->constrained('users');
            $table->text('manual_entry_reason')->nullable();
            $table->datetime('original_timestamp')->nullable(); // If adjusted
            $table->text('adjustment_notes')->nullable();

            // Payroll integration
            $table->boolean('is_processed')->default(false);
            $table->datetime('processed_at')->nullable();
            $table->string('payroll_batch_id')->nullable();
            $table->decimal('hourly_rate', 8, 4)->nullable(); // Rate at time of work
            $table->decimal('gross_pay', 10, 2)->nullable();

            // Anomaly detection
            $table->boolean('is_anomaly')->default(false);
            $table->json('anomaly_flags')->nullable(); // Late, early, missing pair, etc.
            $table->text('anomaly_notes')->nullable();

            // Photo verification (for some systems)
            $table->string('photo_path')->nullable();
            $table->boolean('photo_verified')->default(false);

            // Related timesheet
            $table->foreignId('timesheet_id')->nullable(); // If using timesheet grouping
            $table->integer('timesheet_week')->nullable();
            $table->integer('timesheet_year')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'work_date']);
            $table->index(['user_id', 'entry_type', 'work_date']);
            $table->index(['work_date', 'entry_type']);
            $table->index(['status', 'work_date']);
            $table->index(['active_task_id', 'timestamp']);
            $table->index(['is_processed', 'work_date']);
            $table->index(['approved_by', 'approved_at']);
            $table->index(['work_type', 'work_date']);
            $table->index(['timesheet_week', 'timesheet_year']);
            $table->index(['is_anomaly']);
            $table->index(['timestamp']);

            // Unique constraint to prevent duplicate entries
            $table->unique(['user_id', 'entry_type', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clock_entries');
    }
};