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
        Schema::table('seed_catalog', function (Blueprint $table) {
            $table->dropColumn([
                'market_tier',
                'flavor_profile',
                'description',
                'seasonal_notes',
                'target_germination_rate',
                'storage_requirements',
                'suppliers',
                'avg_price_per_lb',
                'typical_shelf_life_months',
                'is_organic_available',
                'growing_tips',
                'image_url',
                'success_rate'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seed_catalog', function (Blueprint $table) {
            // Add back the removed columns for rollback
            $table->string('market_tier')->nullable();
            $table->text('flavor_profile')->nullable();
            $table->text('description')->nullable();
            $table->json('seasonal_notes')->nullable();
            $table->decimal('target_germination_rate', 5, 1)->nullable();
            $table->json('storage_requirements')->nullable();
            $table->json('suppliers')->nullable();
            $table->decimal('avg_price_per_lb', 8, 2)->nullable();
            $table->integer('typical_shelf_life_months')->nullable();
            $table->boolean('is_organic_available')->default(false);
            $table->text('growing_tips')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('success_rate', 5, 1)->nullable();

            // Re-add the dropped index
            $table->index(['market_tier', 'is_active'], 'seed_catalog_market_tier_is_active_index');
        });
    }
};
