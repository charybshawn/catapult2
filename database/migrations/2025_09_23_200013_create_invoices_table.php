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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // Invoice details
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', [
                'draft',        // Not sent yet
                'sent',         // Sent to customer
                'viewed',       // Customer opened it
                'partial',      // Partially paid
                'paid',         // Fully paid
                'overdue',      // Past due date
                'cancelled',    // Cancelled
                'refunded'      // Refunded
            ])->default('draft');

            // Financial details
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 4)->default(0); // Tax percentage
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_charge', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2)->storedAs('total_amount - amount_paid');

            // Payment terms
            $table->integer('payment_terms_days')->default(30);
            $table->enum('payment_method', [
                'invoice', 'credit_card', 'cash', 'check', 'ach', 'stripe'
            ])->default('invoice');

            // Customer information (snapshot at time of invoice)
            $table->json('customer_snapshot')->nullable(); // Name, address, etc.
            $table->text('billing_address')->nullable();
            $table->string('customer_po_number')->nullable();

            // Communication tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('first_payment_at')->nullable();
            $table->timestamp('fully_paid_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminder_sent_at')->nullable();

            // Notes and references
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->json('line_items')->nullable(); // Invoice line items snapshot

            // File attachments
            $table->string('pdf_path')->nullable();
            $table->json('attachments')->nullable(); // Additional files

            // Integration tracking
            $table->string('stripe_invoice_id')->nullable();
            $table->json('external_ids')->nullable(); // QuickBooks, etc.

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['invoice_date', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['order_id']);
            $table->index(['status', 'amount_due']);
            $table->index(['payment_method', 'status']);
            $table->index(['sent_at']);
            $table->index(['fully_paid_at']);
            $table->index(['stripe_invoice_id']);
            $table->index(['created_by', 'invoice_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};