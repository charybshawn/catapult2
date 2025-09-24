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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->nullable()->constrained()->nullOnDelete();

            // Product details
            $table->string('product_name'); // Could be custom/special blends
            $table->string('variety'); // Sunflower, Pea, Mix, etc.
            $table->text('description')->nullable();
            $table->string('sku')->nullable(); // Product SKU if standardized

            // Quantity and pricing
            $table->decimal('quantity', 10, 2);
            $table->string('unit'); // oz, lbs, grams, flats, trays
            $table->decimal('unit_price', 10, 4);
            $table->decimal('total_price', 10, 2);
            $table->decimal('cost_per_unit', 10, 4)->nullable(); // Internal cost tracking

            // Special requirements
            $table->boolean('requires_organic')->default(false);
            $table->json('special_growing_requirements')->nullable(); // Custom grow specs
            $table->text('customer_specifications')->nullable(); // Size, appearance, etc.
            $table->integer('custom_grow_days')->nullable(); // Override recipe timing

            // Production allocation
            $table->foreignId('allocated_crop_batch_id')->nullable()->constrained('crop_batches');
            $table->foreignId('allocated_harvest_id')->nullable()->constrained('harvests');
            $table->decimal('allocated_quantity', 10, 2)->nullable();
            $table->enum('allocation_status', [
                'pending',      // Not allocated yet
                'allocated',    // Assigned to specific batch/harvest
                'growing',      // In production
                'ready',        // Harvested and available
                'fulfilled',    // Delivered to customer
                'cancelled'     // Cancelled
            ])->default('pending');

            // Quality requirements
            $table->enum('quality_grade_required', ['A', 'B', 'C'])->default('A');
            $table->json('quality_specifications')->nullable(); // Color, size, freshness
            $table->boolean('requires_lot_tracking')->default(false);

            // Fulfillment tracking
            $table->decimal('quantity_fulfilled', 10, 2)->default(0);
            $table->decimal('quantity_remaining', 10, 2)->storedAs('quantity - quantity_fulfilled');
            $table->timestamp('production_started_at')->nullable();
            $table->timestamp('ready_for_harvest_at')->nullable();
            $table->timestamp('harvested_at')->nullable();
            $table->timestamp('packaged_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Packaging details
            $table->string('package_type')->nullable(); // Clamshell, bag, bulk
            $table->string('package_size')->nullable(); // 2oz, 4oz, 1lb
            $table->integer('package_count')->nullable();
            $table->json('packaging_requirements')->nullable(); // Labels, stickers

            // Lot tracking for food safety
            $table->string('lot_number')->nullable();
            $table->json('source_seed_lots')->nullable(); // Which seed lots used
            $table->date('harvest_date')->nullable();
            $table->date('best_by_date')->nullable();

            // Pricing details
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->boolean('is_promotional')->default(false);
            $table->string('promotion_code')->nullable();

            // Custom fields for special orders
            $table->json('custom_attributes')->nullable(); // Flexible metadata
            $table->text('production_notes')->nullable();
            $table->text('fulfillment_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['order_id', 'allocation_status']);
            $table->index(['recipe_id', 'allocation_status']);
            $table->index(['allocated_crop_batch_id']);
            $table->index(['allocated_harvest_id']);
            $table->index(['variety', 'requires_organic']);
            $table->index(['allocation_status', 'production_started_at']);
            $table->index(['lot_number']);
            $table->index(['harvest_date']);
            $table->index(['ready_for_harvest_at']);
            $table->index(['quantity_remaining']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};