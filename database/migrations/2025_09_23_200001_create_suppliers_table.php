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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('payment_terms')->nullable(); // Net 30, COD, etc.
            $table->json('shipping_info')->nullable(); // Lead times, methods, etc.
            $table->json('certifications')->nullable(); // Organic, Non-GMO, etc.
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->integer('rating')->nullable()->comment('1-5 star rating');
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'is_preferred']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};