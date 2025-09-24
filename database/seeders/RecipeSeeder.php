<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user for created_by
        $firstUser = DB::table('users')->first();
        if (!$firstUser) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Get consumables for seed references
        $consumables = DB::table('consumables')->where('category', 'seeds')->get();
        if ($consumables->isEmpty()) {
            $this->command->error('No seed consumables found. Please run ConsumableSeeder first.');
            return;
        }

        $recipes = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Pea Shoots',
                'variety' => 'Green Pea',
                'description' => 'Sweet and tender pea shoots, perfect for salads and garnish',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-PEA-001')->first()->id,
                'seed_density_per_tray' => 50.0,
                'tray_type' => '10x20',
                'soak_hours' => 12,
                'germination_days' => 2,
                'blackout_days' => 3,
                'light_days' => 5,
                'temperature_ranges' => json_encode(['min' => 18, 'max' => 22]),
                'humidity_ranges' => json_encode(['min' => 65, 'max' => 75]),
                'watering_schedule' => json_encode([
                    'germination' => 'Mist twice daily',
                    'blackout' => 'Mist twice daily, avoid overwatering',
                    'light' => 'Mist as needed to keep medium moist'
                ]),
                'expected_yield_grams' => 200.0,
                'minimum_acceptable_yield' => 150.0,
                'cost_per_tray' => 12.50,
                'selling_price_per_unit' => 4.50,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '8-12cm',
                    'color' => 'Bright green',
                    'texture' => 'Crisp and tender'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '2cm above growing medium',
                    'timing' => 'When leaves are fully opened',
                    'handling' => 'Gentle harvest, avoid bruising'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => false,
                'success_rate' => 95.0,
                'times_used' => 0,
                'notes' => 'Popular variety with sweet pea flavor. Great for beginners.',
                'growing_tips' => 'Keep soil evenly moist but not waterlogged. Harvest when leaves are fully opened.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Sunflower Microgreens',
                'variety' => 'Black Oil Sunflower',
                'description' => 'Nutty flavored sunflower microgreens with high protein content',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-SUN-001')->first()->id,
                'seed_density_per_tray' => 40.0,
                'tray_type' => '10x20',
                'soak_hours' => 8,
                'germination_days' => 2,
                'blackout_days' => 4,
                'light_days' => 6,
                'temperature_ranges' => json_encode(['min' => 20, 'max' => 24]),
                'humidity_ranges' => json_encode(['min' => 60, 'max' => 70]),
                'watering_schedule' => json_encode([
                    'soak' => 'Remove hulls before soaking',
                    'germination' => 'Weight down for first 2 days',
                    'blackout' => 'Mist regularly, ensure drainage',
                    'light' => 'Regular misting, avoid overwatering'
                ]),
                'expected_yield_grams' => 180.0,
                'minimum_acceptable_yield' => 140.0,
                'cost_per_tray' => 6.00,
                'selling_price_per_unit' => 5.00,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '10-15cm',
                    'color' => 'Bright green with yellow stems',
                    'texture' => 'Crunchy, substantial'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '3cm above growing medium',
                    'timing' => 'When stems are 2-3 inches tall',
                    'handling' => 'Remove any unsprouted seeds before harvest'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => false,
                'is_specialty' => false,
                'success_rate' => 90.0,
                'times_used' => 0,
                'notes' => 'Nutty flavor, high in protein. Remove any unsprouted seeds.',
                'growing_tips' => 'Remove hulls before soaking. Weight down during early germination.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Radish Microgreens',
                'variety' => 'Red Arrow',
                'description' => 'Spicy, colorful radish microgreens with quick growth',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-RAD-001')->first()->id,
                'seed_density_per_tray' => 15.0,
                'tray_type' => '10x20',
                'soak_hours' => 4,
                'germination_days' => 1,
                'blackout_days' => 2,
                'light_days' => 5,
                'temperature_ranges' => json_encode(['min' => 16, 'max' => 20]),
                'humidity_ranges' => json_encode(['min' => 70, 'max' => 80]),
                'watering_schedule' => json_encode([
                    'germination' => 'Light misting, seeds germinate quickly',
                    'blackout' => 'Gentle misting to avoid displacing seeds',
                    'light' => 'Regular misting, maintain moisture'
                ]),
                'expected_yield_grams' => 120.0,
                'minimum_acceptable_yield' => 90.0,
                'cost_per_tray' => 5.25,
                'selling_price_per_unit' => 6.00,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '5-8cm',
                    'color' => 'Green with purple/red stems',
                    'texture' => 'Crisp and spicy'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '2cm above growing medium',
                    'timing' => 'When cotyledons are fully opened',
                    'handling' => 'Quick harvest to maintain crispness'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => false,
                'success_rate' => 96.0,
                'times_used' => 0,
                'notes' => 'Spicy, peppery flavor. Fast growing variety.',
                'growing_tips' => 'Seeds are small - distribute evenly. Fast germination, watch carefully.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Broccoli Microgreens',
                'variety' => 'Calabrese',
                'description' => 'Mild broccoli flavored microgreens high in sulforaphane',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-BRO-001')->first()->id,
                'seed_density_per_tray' => 8.0,
                'tray_type' => '10x20',
                'soak_hours' => 6,
                'germination_days' => 2,
                'blackout_days' => 3,
                'light_days' => 5,
                'temperature_ranges' => json_encode(['min' => 18, 'max' => 22]),
                'humidity_ranges' => json_encode(['min' => 65, 'max' => 75]),
                'watering_schedule' => json_encode([
                    'germination' => 'Light, frequent misting',
                    'blackout' => 'Maintain consistent moisture',
                    'light' => 'Strong light for deep green color'
                ]),
                'expected_yield_grams' => 100.0,
                'minimum_acceptable_yield' => 75.0,
                'cost_per_tray' => 3.60,
                'selling_price_per_unit' => 7.50,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '5-7cm',
                    'color' => 'Deep green',
                    'texture' => 'Tender, mild flavor'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '2cm above growing medium',
                    'timing' => 'When first true leaves appear',
                    'handling' => 'Gentle harvest, delicate leaves'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => true,
                'success_rate' => 92.0,
                'times_used' => 0,
                'notes' => 'Mild broccoli flavor, very nutritious. High in sulforaphane.',
                'growing_tips' => 'Seeds are very small - distribute evenly. Provide strong light for color.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Arugula Microgreens',
                'variety' => 'Roquette',
                'description' => 'Peppery arugula microgreens perfect for gourmet applications',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-ARU-001')->first()->id,
                'seed_density_per_tray' => 6.0,
                'tray_type' => '10x20',
                'soak_hours' => 2,
                'germination_days' => 1,
                'blackout_days' => 2,
                'light_days' => 6,
                'temperature_ranges' => json_encode(['min' => 16, 'max' => 20]),
                'humidity_ranges' => json_encode(['min' => 60, 'max' => 70]),
                'watering_schedule' => json_encode([
                    'soak' => 'Brief soak only - seeds can become mucilaginous',
                    'germination' => 'Gentle misting to avoid clumping',
                    'blackout' => 'Light misting',
                    'light' => 'Regular watering until harvest'
                ]),
                'expected_yield_grams' => 80.0,
                'minimum_acceptable_yield' => 60.0,
                'cost_per_tray' => 3.30,
                'selling_price_per_unit' => 8.00,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '4-6cm',
                    'color' => 'Dark green',
                    'texture' => 'Tender with peppery bite'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '1.5cm above growing medium',
                    'timing' => 'When first true leaves begin to appear',
                    'handling' => 'Handle carefully - delicate leaves'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => true,
                'success_rate' => 94.0,
                'times_used' => 0,
                'notes' => 'Peppery, spicy flavor. Great for salads and garnish.',
                'growing_tips' => 'Brief soak only. Spread thinly to avoid overcrowding.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Red Russian Kale',
                'variety' => 'Red Russian',
                'description' => 'Colorful kale microgreens with beautiful red stems',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-KAL-001')->first()->id,
                'seed_density_per_tray' => 10.0,
                'tray_type' => '10x20',
                'soak_hours' => 6,
                'germination_days' => 2,
                'blackout_days' => 3,
                'light_days' => 7,
                'temperature_ranges' => json_encode(['min' => 18, 'max' => 22]),
                'humidity_ranges' => json_encode(['min' => 65, 'max' => 75]),
                'watering_schedule' => json_encode([
                    'germination' => 'Maintain consistent moisture',
                    'blackout' => 'Regular misting',
                    'light' => 'Longer growing period for fuller flavor'
                ]),
                'expected_yield_grams' => 140.0,
                'minimum_acceptable_yield' => 110.0,
                'cost_per_tray' => 4.00,
                'selling_price_per_unit' => 6.50,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '6-8cm',
                    'color' => 'Green leaves with red stems',
                    'texture' => 'Tender, mild kale flavor'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '2cm above growing medium',
                    'timing' => 'When cotyledons are fully open',
                    'handling' => 'Harvest carefully to preserve color'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => true,
                'success_rate' => 91.0,
                'times_used' => 0,
                'notes' => 'Mild kale flavor, beautiful red stems. Highly nutritious.',
                'growing_tips' => 'Avoid overcrowding. Longer growing period enhances flavor.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Wheatgrass',
                'variety' => 'Hard Red Winter Wheat',
                'description' => 'Dense wheatgrass for juicing applications',
                'seed_consumable_id' => $consumables->where('sku', 'SEED-WGR-001')->first()->id,
                'seed_density_per_tray' => 60.0,
                'tray_type' => '10x20',
                'soak_hours' => 8,
                'germination_days' => 2,
                'blackout_days' => 3,
                'light_days' => 5,
                'temperature_ranges' => json_encode(['min' => 18, 'max' => 22]),
                'humidity_ranges' => json_encode(['min' => 60, 'max' => 70]),
                'watering_schedule' => json_encode([
                    'germination' => 'Keep very moist during germination',
                    'blackout' => 'Frequent misting but ensure drainage',
                    'light' => 'Regular watering for grass-like growth'
                ]),
                'expected_yield_grams' => 220.0,
                'minimum_acceptable_yield' => 180.0,
                'cost_per_tray' => 4.80,
                'selling_price_per_unit' => 3.50,
                'selling_unit' => '100g',
                'quality_metrics' => json_encode([
                    'height' => '15-20cm',
                    'color' => 'Bright green',
                    'texture' => 'Grass-like, sweet flavor'
                ]),
                'harvest_specifications' => json_encode([
                    'cut_height' => '3cm above growing medium',
                    'timing' => 'At 6-8 inches for juicing',
                    'handling' => 'Cut cleanly for maximum juice yield'
                ]),
                'version' => '1.0',
                'status' => 'active',
                'is_organic' => true,
                'is_specialty' => false,
                'success_rate' => 93.0,
                'times_used' => 0,
                'notes' => 'Grown primarily for juicing. Sweet, grassy flavor.',
                'growing_tips' => 'Dense planting for grass-like growth. Harvest at optimal height.',
                'created_by' => $firstUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($recipes as $recipe) {
            DB::table('recipes')->insert($recipe);
        }

        $this->command->info('Created ' . count($recipes) . ' microgreens recipes');
    }
}