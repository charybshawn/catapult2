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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('consumable_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_lot_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('transaction_type', [
                'purchase',      // Inventory in from supplier
                'adjustment',    // Manual stock adjustment
                'consumption',   // Used in production
                'transfer',      // Between locations
                'waste',         // Expired/damaged/contaminated
                'return',        // Return to supplier
                'count'          // Physical count adjustment
            ]);
            $table->decimal('quantity', 12, 4); // Positive for in, negative for out
            $table->string('unit_of_measure');
            $table->decimal('unit_cost', 10, 4)->nullable();
            $table->string('reference_type')->nullable(); // Model type (crop_batch, order, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Related record ID
            $table->text('reason')->nullable(); // Reason for adjustment/waste
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users');
            $table->timestamp('transaction_date');
            $table->timestamps();

            // Indexes
            $table->index(['consumable_id', 'transaction_date']);
            $table->index(['seed_lot_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']); // Polymorphic index for reference
            $table->index(['performed_by', 'transaction_date']);
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};