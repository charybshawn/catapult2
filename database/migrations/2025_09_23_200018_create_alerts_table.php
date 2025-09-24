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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('title');
            $table->text('message');

            // Alert classification
            $table->enum('alert_type', [
                'production',       // Production-related alerts
                'inventory',        // Stock levels, expiration
                'quality',          // Quality issues, contamination
                'schedule',         // Timing, deadlines
                'equipment',        // Equipment failures, maintenance
                'environmental',    // Temperature, humidity
                'financial',        // Payment due, profitability
                'customer',         // Customer notifications
                'system',           // System errors, performance
                'safety',           // Safety issues, compliance
                'general'           // General notifications
            ]);
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Alert source - what triggered this alert
            $table->string('source_type')->nullable(); // crop_batch, inventory, order, etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->json('source_data')->nullable(); // Additional context data

            // Alert conditions
            $table->string('condition_type')->nullable(); // threshold, schedule, event
            $table->json('condition_config')->nullable(); // Threshold values, rules
            $table->json('current_values')->nullable(); // Values that triggered alert
            $table->text('condition_description')->nullable();

            // Status tracking
            $table->enum('status', [
                'active',           // Alert is active
                'acknowledged',     // Someone has seen it
                'in_progress',      // Being worked on
                'resolved',         // Issue resolved
                'dismissed',        // Manually dismissed
                'expired',          // Automatically expired
                'escalated'         // Escalated to higher priority
            ])->default('active');

            // Response tracking
            $table->foreignId('acknowledged_by')->nullable()->constrained('users');
            $table->datetime('acknowledged_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->datetime('assigned_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->datetime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            // Escalation rules
            $table->boolean('can_escalate')->default(false);
            $table->integer('escalation_minutes')->nullable(); // Auto-escalate after X minutes
            $table->foreignId('escalate_to_user_id')->nullable()->constrained('users');
            $table->json('escalation_chain')->nullable(); // User IDs for escalation
            $table->integer('escalation_level')->default(0);
            $table->datetime('escalated_at')->nullable();

            // Notification settings
            $table->boolean('send_notifications')->default(true);
            $table->json('notification_channels')->nullable(); // email, sms, slack, etc.
            $table->json('notification_recipients')->nullable(); // User IDs or roles
            $table->datetime('first_notification_sent')->nullable();
            $table->datetime('last_notification_sent')->nullable();
            $table->integer('notification_count')->default(0);
            $table->boolean('notifications_paused')->default(false);

            // Repeat handling
            $table->boolean('is_recurring')->default(false);
            $table->datetime('last_occurrence')->nullable();
            $table->integer('occurrence_count')->default(1);
            $table->boolean('suppress_duplicates')->default(true);
            $table->integer('suppress_minutes')->default(60);

            // Auto-resolution
            $table->boolean('can_auto_resolve')->default(false);
            $table->json('auto_resolve_conditions')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->boolean('auto_resolved')->default(false);

            // Action items
            $table->json('recommended_actions')->nullable(); // Suggested next steps
            $table->json('action_links')->nullable(); // URLs to relevant pages
            $table->boolean('has_quick_actions')->default(false);
            $table->json('quick_actions')->nullable(); // Buttons for common actions

            // Performance tracking
            $table->integer('time_to_acknowledge_minutes')->nullable();
            $table->integer('time_to_resolve_minutes')->nullable();
            $table->boolean('was_resolved_on_time')->nullable();
            $table->enum('resolution_quality', ['poor', 'fair', 'good', 'excellent'])->nullable();

            // Related alerts
            $table->foreignId('parent_alert_id')->nullable()->constrained('alerts');
            $table->json('related_alert_ids')->nullable(); // IDs of related alerts
            $table->boolean('is_grouped')->default(false);
            $table->string('group_key')->nullable(); // For grouping similar alerts

            // Metadata
            $table->json('metadata')->nullable(); // Additional context
            $table->json('tags')->nullable(); // For categorization
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'severity']);
            $table->index(['alert_type', 'status']);
            $table->index(['priority', 'created_at']);
            $table->index(['source_type', 'source_id']);
            $table->index(['assigned_to', 'status']);
            $table->index(['acknowledged_by', 'acknowledged_at']);
            $table->index(['resolved_by', 'resolved_at']);
            $table->index(['expires_at', 'status']);
            $table->index(['escalation_level', 'can_escalate']);
            $table->index(['is_recurring', 'last_occurrence']);
            $table->index(['group_key']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};