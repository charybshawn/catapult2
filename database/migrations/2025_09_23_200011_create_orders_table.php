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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // Order classification
            $table->enum('order_type', ['single', 'recurring', 'standing'])->default('single');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', [
                'draft',        // Being created
                'pending',      // Awaiting approval
                'confirmed',    // Approved, in production
                'in_production', // Crops growing
                'ready',        // Ready for harvest/delivery
                'fulfilled',    // Delivered
                'cancelled',    // Cancelled
                'on_hold'       // Temporarily paused
            ])->default('draft');

            // Dates and timing
            $table->date('order_date');
            $table->date('requested_delivery_date');
            $table->date('confirmed_delivery_date')->nullable();
            $table->time('delivery_time_start')->nullable();
            $table->time('delivery_time_end')->nullable();
            $table->integer('lead_time_days')->nullable();

            // Recurring order settings
            $table->enum('recurring_frequency', [
                'weekly', 'bi_weekly', 'monthly', 'custom'
            ])->nullable();
            $table->json('recurring_days')->nullable(); // [1,3,5] for Mon, Wed, Fri
            $table->date('recurring_start_date')->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->integer('recurring_occurrences')->nullable();
            $table->foreignId('parent_order_id')->nullable()->constrained('orders'); // For generated recurring orders

            // Financial details
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_charge', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Delivery details
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_postal_code')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->enum('delivery_method', ['delivery', 'pickup', 'shipping'])->default('delivery');
            $table->string('delivery_contact_person')->nullable();
            $table->string('delivery_contact_phone')->nullable();

            // Production planning
            $table->date('production_start_date')->nullable();
            $table->boolean('requires_organic')->default(false);
            $table->json('special_requirements')->nullable(); // Custom growing specs
            $table->text('production_notes')->nullable();

            // Fulfillment tracking
            $table->timestamp('production_started_at')->nullable();
            $table->timestamp('harvest_started_at')->nullable();
            $table->timestamp('packaging_completed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('delivered_by')->nullable()->constrained('users');

            // Quality and compliance
            $table->boolean('requires_lot_tracking')->default(false);
            $table->json('quality_requirements')->nullable();
            $table->text('customer_po_number')->nullable();
            $table->json('certifications_needed')->nullable(); // Organic, food safety

            // Communication
            $table->boolean('customer_notified')->default(false);
            $table->timestamp('last_customer_notification')->nullable();
            $table->json('notification_log')->nullable();

            // Internal tracking
            $table->foreignId('sales_rep_id')->nullable()->constrained('users');
            $table->foreignId('assigned_grower_id')->nullable()->constrained('users');
            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();

            // Payment tracking
            $table->enum('payment_status', [
                'pending', 'paid', 'partial', 'overdue', 'refunded'
            ])->default('pending');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('payment_due_date')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['status', 'requested_delivery_date']);
            $table->index(['order_date', 'status']);
            $table->index(['confirmed_delivery_date', 'status']);
            $table->index(['order_type', 'recurring_frequency']);
            $table->index(['parent_order_id']);
            $table->index(['priority', 'status']);
            $table->index(['sales_rep_id', 'status']);
            $table->index(['assigned_grower_id', 'status']);
            $table->index(['payment_status', 'payment_due_date']);
            $table->index(['delivery_postal_code']);
            $table->index(['created_by', 'order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};