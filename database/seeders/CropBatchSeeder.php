<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CropBatchSeeder extends Seeder
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

        // Get recipes for batch references
        $recipes = DB::table('recipes')->where('status', 'active')->get();
        if ($recipes->isEmpty()) {
            $this->command->error('No recipes found. Please run RecipeSeeder first.');
            return;
        }

        // Get crop stages
        $stages = DB::table('crop_stages')->orderBy('sort_order')->get();
        if ($stages->isEmpty()) {
            $this->command->error('No crop stages found.');
            return;
        }

        // Check existing batches to get next available number
        $lastBatch = DB::table('crop_batches')->orderBy('id', 'desc')->first();
        $batchCounter = $lastBatch ? (intval(substr($lastBatch->batch_number, -4)) + 1) : 1;
        $now = now();

        $batches = [
            // Batch ready to harvest
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Pea Shoots')->first()->id,
                'tray_count' => 5,
                'tray_range' => 'A1-A5',
                'planned_plant_date' => $now->copy()->subDays(10)->toDateString(),
                'planned_harvest_date' => $now->toDateString(),
                'actual_plant_date' => $now->copy()->subDays(10)->toDateString(),
                'soak_started_at' => $now->copy()->subDays(10),
                'germination_started_at' => $now->copy()->subDays(8),
                'blackout_started_at' => $now->copy()->subDays(6),
                'light_started_at' => $now->copy()->subDays(3),
                'ready_at' => $now->copy()->subHours(2),
                'current_stage_id' => $stages->where('slug', 'ready')->first()->id,
                'status' => 'active',
                'total_seeds_used' => 250.0,
                'growing_location' => 'Main Growing Area - Rack A',
                'environmental_conditions' => json_encode([
                    'temperature' => '20°C',
                    'humidity' => '70%',
                    'light_hours' => '16'
                ]),
                'germination_rate_percent' => 95,
                'quality_notes' => 'Excellent growth, perfect color and texture',
                'expected_yield' => 1000.0,
                'total_labor_hours' => 3.5,
                'total_labor_cost' => 52.50,
                'notes' => 'High quality batch ready for harvest',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subHours(2),
            ],

            // Batch in light stage
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Radish Microgreens')->first()->id,
                'tray_count' => 4,
                'tray_range' => 'B1-B4',
                'planned_plant_date' => $now->copy()->subDays(6)->toDateString(),
                'planned_harvest_date' => $now->copy()->addDays(2)->toDateString(),
                'actual_plant_date' => $now->copy()->subDays(6)->toDateString(),
                'soak_started_at' => $now->copy()->subDays(6),
                'germination_started_at' => $now->copy()->subDays(5),
                'blackout_started_at' => $now->copy()->subDays(4),
                'light_started_at' => $now->copy()->subDays(2),
                'current_stage_id' => $stages->where('slug', 'light')->first()->id,
                'status' => 'active',
                'total_seeds_used' => 60.0,
                'growing_location' => 'Main Growing Area - Rack B',
                'environmental_conditions' => json_encode([
                    'temperature' => '18°C',
                    'humidity' => '75%',
                    'light_hours' => '16'
                ]),
                'germination_rate_percent' => 96,
                'quality_notes' => 'Good spicy flavor developing, nice purple color',
                'expected_yield' => 480.0,
                'total_labor_hours' => 2.0,
                'total_labor_cost' => 30.00,
                'notes' => 'Progressing well under lights',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // Batch in blackout stage
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Sunflower Microgreens')->first()->id,
                'tray_count' => 3,
                'tray_range' => 'C1-C3',
                'planned_plant_date' => $now->copy()->subDays(4)->toDateString(),
                'planned_harvest_date' => $now->copy()->addDays(8)->toDateString(),
                'actual_plant_date' => $now->copy()->subDays(4)->toDateString(),
                'soak_started_at' => $now->copy()->subDays(4),
                'germination_started_at' => $now->copy()->subDays(2),
                'blackout_started_at' => $now->copy()->subDays(1),
                'current_stage_id' => $stages->where('slug', 'blackout')->first()->id,
                'status' => 'active',
                'total_seeds_used' => 120.0,
                'growing_location' => 'Blackout Area - Shelf C',
                'environmental_conditions' => json_encode([
                    'temperature' => '22°C',
                    'humidity' => '65%',
                    'light_hours' => '0'
                ]),
                'germination_rate_percent' => 88,
                'quality_notes' => 'Good stem elongation in blackout',
                'expected_yield' => 540.0,
                'total_labor_hours' => 1.5,
                'total_labor_cost' => 22.50,
                'notes' => 'Developing well in blackout stage',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(1),
            ],

            // Batch in germination stage
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Broccoli Microgreens')->first()->id,
                'tray_count' => 2,
                'tray_range' => 'D1-D2',
                'planned_plant_date' => $now->copy()->subDays(2)->toDateString(),
                'planned_harvest_date' => $now->copy()->addDays(8)->toDateString(),
                'actual_plant_date' => $now->copy()->subDays(2)->toDateString(),
                'soak_started_at' => $now->copy()->subDays(2),
                'germination_started_at' => $now->copy()->subDays(1),
                'current_stage_id' => $stages->where('slug', 'germination')->first()->id,
                'status' => 'active',
                'total_seeds_used' => 16.0,
                'growing_location' => 'Germination Area - Shelf D',
                'environmental_conditions' => json_encode([
                    'temperature' => '20°C',
                    'humidity' => '70%',
                    'light_hours' => '0'
                ]),
                'germination_rate_percent' => 90,
                'quality_notes' => 'Germination proceeding well',
                'expected_yield' => 200.0,
                'total_labor_hours' => 0.5,
                'total_labor_cost' => 7.50,
                'notes' => 'Small seeds but good germination rate',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(1),
            ],

            // Recently started batch
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Arugula Microgreens')->first()->id,
                'tray_count' => 3,
                'tray_range' => 'E1-E3',
                'planned_plant_date' => $now->toDateString(),
                'planned_harvest_date' => $now->copy()->addDays(9)->toDateString(),
                'actual_plant_date' => $now->toDateString(),
                'soak_started_at' => $now->copy()->subHours(6),
                'current_stage_id' => $stages->where('slug', 'soaking')->first()->id,
                'status' => 'active',
                'total_seeds_used' => 18.0,
                'growing_location' => 'Prep Area - Station E',
                'environmental_conditions' => json_encode([
                    'temperature' => '18°C',
                    'humidity' => '65%',
                    'light_hours' => '0'
                ]),
                'quality_notes' => 'Seeds soaking well',
                'expected_yield' => 240.0,
                'total_labor_hours' => 0.25,
                'total_labor_cost' => 3.75,
                'notes' => 'Fresh batch just started soaking',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6),
            ],

            // Completed batch
            [
                'uuid' => Str::uuid(),
                'batch_number' => 'CB-' . str_pad($batchCounter++, 4, '0', STR_PAD_LEFT),
                'recipe_id' => $recipes->where('name', 'Red Russian Kale')->first()->id,
                'tray_count' => 2,
                'tray_range' => 'F1-F2',
                'planned_plant_date' => $now->copy()->subDays(15)->toDateString(),
                'planned_harvest_date' => $now->copy()->subDays(3)->toDateString(),
                'actual_plant_date' => $now->copy()->subDays(15)->toDateString(),
                'soak_started_at' => $now->copy()->subDays(15),
                'germination_started_at' => $now->copy()->subDays(13),
                'blackout_started_at' => $now->copy()->subDays(11),
                'light_started_at' => $now->copy()->subDays(8),
                'ready_at' => $now->copy()->subDays(3),
                'completed_at' => $now->copy()->subDays(2),
                'current_stage_id' => $stages->where('slug', 'harvested')->first()->id,
                'status' => 'harvested',
                'total_seeds_used' => 20.0,
                'growing_location' => 'Storage - Completed',
                'environmental_conditions' => json_encode([
                    'temperature' => '19°C',
                    'humidity' => '70%',
                    'light_hours' => '16'
                ]),
                'germination_rate_percent' => 91,
                'quality_notes' => 'Beautiful red stems, excellent quality',
                'expected_yield' => 280.0,
                'actual_yield' => 285.0,
                'total_labor_hours' => 4.0,
                'total_labor_cost' => 60.00,
                'notes' => 'Excellent batch, above expected yield',
                'created_by' => $firstUser->id,
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(2),
            ]
        ];

        foreach ($batches as $batch) {
            DB::table('crop_batches')->insert($batch);
        }

        $this->command->info('Created ' . count($batches) . ' crop batches in various stages');
    }
}