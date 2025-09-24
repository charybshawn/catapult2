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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('customer_number')->unique();
            $table->string('business_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Business classification
            $table->enum('customer_type', [
                'restaurant',
                'grocery_store',
                'farmers_market',
                'food_service',
                'distributor',
                'individual',
                'other'
            ]);
            $table->enum('business_size', ['small', 'medium', 'large', 'enterprise'])->nullable();

            // Billing address
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->default('US');

            // Delivery address
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_postal_code')->nullable();
            $table->string('delivery_country')->default('US');
            $table->text('delivery_instructions')->nullable();
            $table->boolean('has_loading_dock')->default(false);
            $table->time('delivery_window_start')->nullable();
            $table->time('delivery_window_end')->nullable();

            // Account settings
            $table->enum('status', ['active', 'inactive', 'suspended', 'prospect'])->default('prospect');
            $table->enum('credit_status', ['good', 'watch', 'hold', 'cash_only'])->default('good');
            $table->decimal('credit_limit', 10, 2)->nullable();
            $table->integer('payment_terms_days')->default(30); // Net 30, etc.
            $table->enum('payment_method', ['invoice', 'credit_card', 'cash', 'check', 'ach'])->default('invoice');

            // Pricing and discounts
            $table->enum('price_tier', ['standard', 'volume', 'premium', 'wholesale'])->default('standard');
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('minimum_order_amount', 8, 2)->nullable();
            $table->boolean('requires_organic_only')->default(false);

            // Delivery preferences
            $table->json('preferred_delivery_days')->nullable(); // [1,3,5] for Mon, Wed, Fri
            $table->enum('delivery_frequency', [
                'daily',
                'weekly',
                'bi_weekly',
                'monthly',
                'on_demand'
            ])->default('weekly');
            $table->decimal('delivery_charge', 8, 2)->nullable();
            $table->boolean('delivery_charge_waived')->default(false);
            $table->decimal('delivery_minimum', 8, 2)->nullable();

            // Product preferences
            $table->json('preferred_products')->nullable(); // Product IDs or names
            $table->json('excluded_products')->nullable(); // Allergies, dislikes
            $table->json('custom_specifications')->nullable(); // Special growing requirements

            // Communication preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->json('notification_preferences')->nullable(); // What to notify about

            // Sales tracking
            $table->decimal('total_lifetime_value', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->nullable();
            $table->integer('total_orders')->default(0);
            $table->date('first_order_date')->nullable();
            $table->date('last_order_date')->nullable();
            $table->integer('days_since_last_order')->nullable();

            // Lead tracking
            $table->string('lead_source')->nullable(); // Referral, website, etc.
            $table->date('lead_date')->nullable();
            $table->foreignId('sales_rep_id')->nullable()->constrained('users');
            $table->text('sales_notes')->nullable();

            // Food safety and compliance
            $table->json('certifications_required')->nullable(); // Organic, food safety
            $table->boolean('requires_lot_tracking')->default(false);
            $table->json('food_safety_requirements')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['customer_type', 'status']);
            $table->index(['status', 'is_active']);
            $table->index(['price_tier', 'status']);
            $table->index(['delivery_postal_code']);
            $table->index(['sales_rep_id', 'status']);
            $table->index(['last_order_date']);
            $table->index(['total_lifetime_value']);
            $table->index(['payment_terms_days', 'credit_status']);
            $table->index('business_name');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};