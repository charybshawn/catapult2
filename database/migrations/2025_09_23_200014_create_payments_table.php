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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('payment_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->timestamp('processed_at')->nullable();
            $table->enum('status', [
                'pending',      // Payment initiated
                'processing',   // Being processed
                'completed',    // Successfully processed
                'failed',       // Payment failed
                'cancelled',    // Cancelled
                'refunded',     // Refunded
                'disputed'      // Disputed/chargeback
            ])->default('pending');

            // Payment method details
            $table->enum('payment_method', [
                'cash',
                'check',
                'credit_card',
                'debit_card',
                'ach',
                'wire_transfer',
                'stripe',
                'paypal',
                'other'
            ]);
            $table->string('payment_reference')->nullable(); // Check number, transaction ID
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable(); // Visa, MasterCard, etc.

            // Processing details
            $table->decimal('processing_fee', 8, 4)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable(); // Amount minus fees
            $table->string('processor')->nullable(); // Stripe, Square, etc.
            $table->string('processor_transaction_id')->nullable();
            $table->string('processor_reference')->nullable();

            // Allocation to invoices
            $table->json('invoice_allocations')->nullable(); // How payment splits across invoices
            $table->decimal('allocated_amount', 10, 2)->nullable();
            $table->decimal('unallocated_amount', 10, 2)->nullable();

            // Banking details
            $table->string('deposit_account')->nullable();
            $table->date('deposited_date')->nullable();
            $table->string('bank_reference')->nullable();

            // Refund details
            $table->foreignId('refunded_payment_id')->nullable()->constrained('payments');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->date('refund_date')->nullable();
            $table->text('refund_reason')->nullable();

            // Communication
            $table->boolean('customer_notified')->default(false);
            $table->timestamp('notification_sent_at')->nullable();

            // Notes and attachments
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // Receipt images, etc.

            // Reconciliation
            $table->boolean('is_reconciled')->default(false);
            $table->date('reconciled_date')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users');

            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index(['payment_date', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['processor_transaction_id']);
            $table->index(['status', 'processed_at']);
            $table->index(['deposited_date']);
            $table->index(['is_reconciled', 'payment_date']);
            $table->index(['processed_by', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};