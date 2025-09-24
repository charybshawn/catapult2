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
        Schema::create('seed_lots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('consumable_id')->constrained()->cascadeOnDelete();
            $table->string('lot_number')->index();
            $table->string('supplier_lot_number')->nullable();
            $table->date('purchase_date');
            $table->date('expiration_date')->nullable();
            $table->date('test_date')->nullable(); // Germination test date
            $table->decimal('germination_rate', 5, 2)->nullable(); // Percentage
            $table->decimal('quantity_received', 12, 4);
            $table->decimal('quantity_remaining', 12, 4);
            $table->decimal('unit_cost', 10, 4); // Cost when purchased
            $table->json('test_results')->nullable(); // Germination tests, purity, etc.
            $table->json('certificates')->nullable(); // Organic, analysis certificates
            $table->enum('status', ['active', 'depleted', 'expired', 'recalled', 'quarantined'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['consumable_id', 'status']);
            $table->index(['lot_number', 'consumable_id']);
            $table->index(['expiration_date', 'status']);
            $table->index(['purchase_date']);
            $table->index(['quantity_remaining']);
            $table->index(['germination_rate']);

            // Unique constraint to prevent duplicate lots for same consumable
            $table->unique(['consumable_id', 'lot_number', 'purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_lots');
    }
};