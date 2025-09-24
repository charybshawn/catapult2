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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', [
                'seeds',
                'growing_media',
                'trays',
                'packaging',
                'cleaning_supplies',
                'nutrients',
                'amendments',
                'tools',
                'other'
            ]);
            $table->enum('subcategory', [
                // Seeds
                'microgreen_seeds', 'herb_seeds', 'sprout_seeds',
                // Growing Media
                'soil', 'coco_coir', 'hemp_mats', 'rockwool', 'perlite',
                // Trays
                '1020_trays', '1010_trays', 'mesh_bottom', 'solid_bottom',
                // Packaging
                'clamshells', 'bags', 'labels', 'boxes',
                // Other categories
                'general'
            ])->default('general');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_sku')->nullable();
            $table->string('unit_of_measure'); // grams, pieces, liters, etc.
            $table->decimal('unit_cost', 10, 4); // Cost per unit
            $table->decimal('current_stock', 12, 4)->default(0);
            $table->decimal('minimum_stock', 12, 4)->default(0);
            $table->decimal('reorder_point', 12, 4)->default(0);
            $table->decimal('maximum_stock', 12, 4)->nullable();
            $table->integer('shelf_life_days')->nullable(); // For perishable items
            $table->json('storage_requirements')->nullable(); // Temperature, humidity, etc.
            $table->json('specifications')->nullable(); // Size, weight, germination rate, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_lot_tracking')->default(false);
            $table->boolean('is_organic')->default(false);
            $table->boolean('is_non_gmo')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['supplier_id', 'is_active']);
            $table->index('current_stock');
            $table->index(['requires_lot_tracking']);
            $table->index(['is_organic', 'is_non_gmo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};