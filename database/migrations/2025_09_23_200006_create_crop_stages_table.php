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
        Schema::create('crop_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order');
            $table->string('color_code', 7)->default('#6B7280'); // Hex color for UI
            $table->string('icon')->nullable(); // Icon class or name

            // Stage characteristics
            $table->boolean('requires_light')->default(false);
            $table->boolean('requires_watering')->default(true);
            $table->boolean('can_harvest')->default(false);
            $table->boolean('is_terminal')->default(false); // Final stage

            // Environmental defaults
            $table->json('default_temperature_range')->nullable();
            $table->json('default_humidity_range')->nullable();
            $table->integer('default_duration_days')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'sort_order']);
            $table->index('sort_order');
        });

        // Insert default crop stages
        DB::table('crop_stages')->insert([
            [
                'name' => 'Soaking',
                'slug' => 'soaking',
                'description' => 'Seeds are soaking in water to begin germination process',
                'sort_order' => 1,
                'color_code' => '#3B82F6',
                'icon' => 'water-drop',
                'requires_light' => false,
                'requires_watering' => false,
                'can_harvest' => false,
                'is_terminal' => false,
                'default_duration_days' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Germination',
                'slug' => 'germination',
                'description' => 'Seeds are sprouting and developing initial roots',
                'sort_order' => 2,
                'color_code' => '#10B981',
                'icon' => 'seedling',
                'requires_light' => false,
                'requires_watering' => true,
                'can_harvest' => false,
                'is_terminal' => false,
                'default_duration_days' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blackout',
                'slug' => 'blackout',
                'description' => 'Growing in darkness to develop stems and initial leaves',
                'sort_order' => 3,
                'color_code' => '#374151',
                'icon' => 'moon',
                'requires_light' => false,
                'requires_watering' => true,
                'can_harvest' => false,
                'is_terminal' => false,
                'default_duration_days' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Light',
                'slug' => 'light',
                'description' => 'Growing under lights to develop color and flavor',
                'sort_order' => 4,
                'color_code' => '#F59E0B',
                'icon' => 'sun',
                'requires_light' => true,
                'requires_watering' => true,
                'can_harvest' => false,
                'is_terminal' => false,
                'default_duration_days' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ready to Harvest',
                'slug' => 'ready',
                'description' => 'Microgreens are mature and ready for harvest',
                'sort_order' => 5,
                'color_code' => '#EF4444',
                'icon' => 'check-circle',
                'requires_light' => true,
                'requires_watering' => true,
                'can_harvest' => true,
                'is_terminal' => false,
                'default_duration_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Harvested',
                'slug' => 'harvested',
                'description' => 'Microgreens have been harvested',
                'sort_order' => 6,
                'color_code' => '#6B7280',
                'icon' => 'archive',
                'requires_light' => false,
                'requires_watering' => false,
                'can_harvest' => false,
                'is_terminal' => true,
                'default_duration_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Failed',
                'slug' => 'failed',
                'description' => 'Crop failed due to contamination, poor germination, or other issues',
                'sort_order' => 7,
                'color_code' => '#DC2626',
                'icon' => 'x-circle',
                'requires_light' => false,
                'requires_watering' => false,
                'can_harvest' => false,
                'is_terminal' => true,
                'default_duration_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_stages');
    }
};