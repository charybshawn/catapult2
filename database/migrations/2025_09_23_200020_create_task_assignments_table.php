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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Assignment details
            $table->enum('assignment_type', [
                'primary',      // Main person responsible
                'secondary',    // Backup or helper
                'observer',     // Needs to know but not do
                'approver',     // Must approve completion
                'reviewer'      // Reviews quality
            ])->default('primary');

            $table->enum('status', [
                'assigned',     // Assigned but not accepted
                'accepted',     // User accepted assignment
                'declined',     // User declined assignment
                'in_progress',  // Currently working
                'completed',    // User finished their part
                'delegated',    // Reassigned to someone else
                'removed'       // Removed from assignment
            ])->default('assigned');

            // Timing
            $table->datetime('assigned_at');
            $table->datetime('accepted_at')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('due_at')->nullable(); // Individual deadline

            // Assignment metadata
            $table->foreignId('assigned_by')->constrained('users');
            $table->text('assignment_notes')->nullable();
            $table->json('role_requirements')->nullable(); // Skills, permissions needed
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('actual_hours', 6, 2)->nullable();

            // Workload and capacity
            $table->integer('workload_percentage')->default(100); // % of task responsibility
            $table->decimal('hourly_rate', 8, 4)->nullable(); // Rate for this assignment
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();

            // Performance tracking
            $table->enum('performance_rating', [
                'excellent', 'good', 'satisfactory', 'needs_improvement', 'unsatisfactory'
            ])->nullable();
            $table->text('performance_notes')->nullable();
            $table->boolean('completed_on_time')->nullable();
            $table->integer('quality_score')->nullable(); // 1-10

            // Communication
            $table->boolean('notifications_enabled')->default(true);
            $table->datetime('last_notification_sent')->nullable();
            $table->integer('notification_count')->default(0);
            $table->json('notification_preferences')->nullable();

            // Delegation tracking
            $table->foreignId('delegated_to')->nullable()->constrained('users');
            $table->datetime('delegated_at')->nullable();
            $table->text('delegation_reason')->nullable();
            $table->foreignId('original_assignee_id')->nullable()->constrained('users');

            // Approval workflow
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->datetime('approved_at')->nullable();
            $table->enum('approval_status', [
                'pending', 'approved', 'rejected', 'needs_revision'
            ])->nullable();
            $table->text('approval_notes')->nullable();

            // Availability and scheduling
            $table->json('availability_windows')->nullable(); // When user can work on this
            $table->boolean('can_work_remotely')->default(false);
            $table->string('preferred_location')->nullable();
            $table->json('scheduling_constraints')->nullable();

            // Skills and qualifications
            $table->json('required_certifications')->nullable();
            $table->json('user_qualifications')->nullable(); // Snapshot at assignment
            $table->boolean('training_required')->default(false);
            $table->datetime('training_completed_at')->nullable();

            // Collaboration
            $table->json('collaboration_notes')->nullable(); // Notes about working with others
            $table->boolean('is_team_lead')->default(false);
            $table->json('team_members')->nullable(); // Other assigned users

            // Progress tracking
            $table->integer('progress_percentage')->default(0);
            $table->json('progress_checkpoints')->nullable(); // Milestones
            $table->datetime('last_progress_update')->nullable();
            $table->text('current_status_notes')->nullable();

            // Mobile and field work
            $table->boolean('supports_mobile')->default(false);
            $table->json('mobile_requirements')->nullable(); // GPS, camera, etc.
            $table->boolean('requires_checkin')->default(false);
            $table->json('checkin_log')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['task_id', 'assignment_type']);
            $table->index(['user_id', 'status']);
            $table->index(['assigned_by', 'assigned_at']);
            $table->index(['status', 'due_at']);
            $table->index(['assignment_type', 'status']);
            $table->index(['delegated_to', 'delegated_at']);
            $table->index(['approved_by', 'approval_status']);
            $table->index(['completed_at']);
            $table->index(['progress_percentage', 'status']);
            $table->index(['is_team_lead', 'task_id']);

            // Unique constraint to prevent duplicate primary assignments
            $table->unique(['task_id', 'user_id', 'assignment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};