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
        Schema::create('seed_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_id')->unique(); // BRASS-ARUG-ROQU
            $table->string('botanical_name'); // Eruca sativa
            $table->string('common_name'); // Arugula
            $table->string('cultivar'); // Roquette
            $table->string('category'); // Brassicas
            $table->string('display_name'); // Arugula - Roquette

            // Growing Parameters
            $table->decimal('seed_density_oz_per_1020', 4, 2)->nullable(); // 0.75
            $table->integer('soak_hours')->default(0); // 0-24 hours
            $table->integer('blackout_days')->default(3); // 2-5 days
            $table->integer('light_days')->default(5); // 4-10 days
            $table->integer('total_days')->default(8); // calculated field

            // Market Data
            $table->enum('market_tier', ['premium', 'standard', 'volume'])->default('standard');
            $table->text('flavor_profile')->nullable(); // peppery, nutty, sweet
            $table->text('description')->nullable();
            $table->json('seasonal_notes')->nullable(); // JSON for seasonal growing tips

            // Quality Standards
            $table->decimal('target_germination_rate', 3, 1)->default(85.0); // 85.0%
            $table->json('storage_requirements')->nullable(); // temp, humidity, etc

            // Supplier Information
            $table->json('suppliers')->nullable(); // Array of supplier data
            $table->decimal('avg_price_per_lb', 6, 2)->nullable();
            $table->integer('typical_shelf_life_months')->nullable();

            // System Fields
            $table->boolean('is_active')->default(true);
            $table->boolean('is_organic_available')->default(false);
            $table->text('growing_tips')->nullable();
            $table->string('image_url')->nullable();

            // Tracking
            $table->integer('usage_count')->default(0); // How many times used in recipes
            $table->timestamp('last_used_at')->nullable();
            $table->decimal('success_rate', 3, 1)->nullable(); // Overall success rate

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['market_tier', 'is_active']);
            $table->index('common_name');
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_catalog');
    }
};
