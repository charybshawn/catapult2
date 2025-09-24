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
        Schema::dropIfExists('crop_batches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table if needed for rollback
        Schema::create('crop_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->foreignId('recipe_id')->constrained('recipes');
            $table->string('variety');
            $table->integer('total_trays');
            $table->date('seeding_date');
            $table->date('expected_harvest_date')->nullable();
            $table->string('current_stage');
            $table->string('status')->default('active');
            $table->json('environmental_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
};