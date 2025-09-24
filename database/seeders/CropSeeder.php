<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing crop data (force delete including soft deletes)
        $this->command->info('Clearing existing crop data...');
        Crop::withTrashed()->forceDelete();
        // Also clear any remaining records with direct DB query
        DB::table('crops')->delete();

        $this->command->info('Creating individual crop tray records...');

        // Define crop batches with their configurations
        $batches = [
            [
                'crop_batch' => 'PS-2024-001',
                'variety' => 'Pea Shoots - Green Pea',
                'tray_count' => 10,
                'tray_type' => '10x20',
                'location_base' => 'Soaking Station A',
                'current_stage_id' => 1, // Soaking
                'status' => 'active',
                'soak_started_at' => now()->subHours(4),
                'seed_weight_per_tray' => 50,
                'notes' => 'Fresh batch of pea shoots for weekly production',
                'tray_prefix' => 'A'
            ],
            [
                'crop_batch' => 'RM-2024-002',
                'variety' => 'Radish Microgreens - Red Arrow',
                'tray_count' => 20,
                'tray_type' => '10x20',
                'location_base' => 'Germination Rack B',
                'current_stage_id' => 2, // Germination
                'status' => 'active',
                'soak_started_at' => now()->subDays(1)->subHours(6),
                'germination_started_at' => now()->subHours(18),
                'seed_weight_per_tray' => 30,
                'notes' => 'High demand radish batch - restaurant order',
                'tray_prefix' => 'B'
            ],
            [
                'crop_batch' => 'SM-2024-003',
                'variety' => 'Sunflower Microgreens - Black Oil',
                'tray_count' => 15,
                'tray_type' => '10x20',
                'location_base' => 'Blackout Shelf C',
                'current_stage_id' => 3, // Blackout
                'status' => 'active',
                'soak_started_at' => now()->subDays(2)->subHours(12),
                'germination_started_at' => now()->subDays(2),
                'blackout_started_at' => now()->subDays(1)->subHours(6),
                'seed_weight_per_tray' => 60,
                'notes' => 'Excellent germination rate - 95%',
                'tray_prefix' => 'C'
            ],
            [
                'crop_batch' => 'AM-2024-004',
                'variety' => 'Arugula Microgreens - Roquette',
                'tray_count' => 12,
                'tray_type' => '10x20',
                'location_base' => 'Light Shelf D',
                'current_stage_id' => 4, // Light
                'status' => 'active',
                'soak_started_at' => now()->subDays(4),
                'germination_started_at' => now()->subDays(3)->subHours(12),
                'blackout_started_at' => now()->subDays(2)->subHours(18),
                'light_started_at' => now()->subDays(1)->subHours(12),
                'seed_weight_per_tray' => 25,
                'notes' => 'Beautiful green color development under lights',
                'tray_prefix' => 'D'
            ],
            [
                'crop_batch' => 'BM-2024-005',
                'variety' => 'Broccoli Microgreens - Calabrese',
                'tray_count' => 24,
                'tray_type' => '10x20',
                'location_base' => 'Harvest Area E',
                'current_stage_id' => 5, // Ready to Harvest
                'status' => 'active',
                'soak_started_at' => now()->subDays(8),
                'germination_started_at' => now()->subDays(7),
                'blackout_started_at' => now()->subDays(5),
                'light_started_at' => now()->subDays(3),
                'ready_at' => now()->subHours(6),
                'seed_weight_per_tray' => 20,
                'estimated_yield_per_tray' => 180,
                'quality_grade' => 'A',
                'notes' => 'Perfect harvest timing - excellent sulforaphane content',
                'tray_prefix' => 'E'
            ],
            [
                'crop_batch' => 'KM-2024-006',
                'variety' => 'Kale Microgreens - Red Russian',
                'tray_count' => 8,
                'tray_type' => '10x20',
                'location_base' => 'Storage',
                'current_stage_id' => 6, // Harvested
                'status' => 'harvested',
                'soak_started_at' => now()->subDays(12),
                'germination_started_at' => now()->subDays(11),
                'blackout_started_at' => now()->subDays(9),
                'light_started_at' => now()->subDays(7),
                'ready_at' => now()->subDays(2)->subHours(8),
                'harvested_at' => now()->subDays(2),
                'seed_weight_per_tray' => 35,
                'actual_yield_per_tray' => 285,
                'yield_unit' => 'grams',
                'quality_grade' => 'A+',
                'notes' => 'Excellent batch - high yield and premium quality',
                'tray_prefix' => 'F'
            ]
        ];

        $totalTrays = 0;

        foreach ($batches as $batch) {
            $this->createBatchTrays($batch);
            $totalTrays += $batch['tray_count'];
            $this->command->info("Created {$batch['tray_count']} trays for batch {$batch['crop_batch']} ({$batch['variety']})");
        }

        $this->command->info("\nSuccessfully created {$totalTrays} individual crop tray records across " . count($batches) . " batches.");
    }

    /**
     * Create individual tray records for a batch
     */
    private function createBatchTrays(array $batchConfig): void
    {
        $trayCount = $batchConfig['tray_count'];
        $trayPrefix = $batchConfig['tray_prefix'];

        for ($i = 1; $i <= $trayCount; $i++) {
            // Generate tray ID (A1, A2, ... A10, B1, B2, etc.)
            if ($i <= 10) {
                $trayId = $trayPrefix . $i;
            } else {
                $row = chr(ord($trayPrefix) + intval(($i - 1) / 10));
                $col = (($i - 1) % 10) + 1;
                $trayId = $row . $col;
            }

            // Calculate position coordinates
            $positionX = (($i - 1) % 10) + 1;
            $positionY = intval(($i - 1) / 10) + 1;

            // Create base tray data
            $trayData = [
                'uuid' => Str::uuid(),
                'crop_batch' => $batchConfig['crop_batch'],
                'tray_id' => $trayId,
                'tray_number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'tray_type' => $batchConfig['tray_type'],
                'current_stage_id' => $batchConfig['current_stage_id'],
                'status' => $batchConfig['status'],
                'location' => $batchConfig['location_base'] . ' - Position ' . $trayId,
                'position_x' => $positionX,
                'position_y' => $positionY,
                'notes' => $batchConfig['notes'] . " (Tray {$trayId})",
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Add timing data based on stage
            if (isset($batchConfig['soak_started_at'])) {
                $trayData['soak_started_at'] = $batchConfig['soak_started_at'];
            }
            if (isset($batchConfig['germination_started_at'])) {
                $trayData['germination_started_at'] = $batchConfig['germination_started_at'];
            }
            if (isset($batchConfig['blackout_started_at'])) {
                $trayData['blackout_started_at'] = $batchConfig['blackout_started_at'];
            }
            if (isset($batchConfig['light_started_at'])) {
                $trayData['light_started_at'] = $batchConfig['light_started_at'];
            }
            if (isset($batchConfig['ready_at'])) {
                $trayData['ready_at'] = $batchConfig['ready_at'];
            }
            if (isset($batchConfig['harvested_at'])) {
                $trayData['harvested_at'] = $batchConfig['harvested_at'];
            }

            // Add yield and quality data
            if (isset($batchConfig['seed_weight_per_tray'])) {
                // Add some realistic variation to seed weights (±5%)
                $variation = rand(-5, 5) / 100;
                $seedWeight = $batchConfig['seed_weight_per_tray'] * (1 + $variation);
                $trayData['total_water_ml'] = round($seedWeight * 2.5); // Realistic water usage
            }

            if (isset($batchConfig['estimated_yield_per_tray'])) {
                $variation = rand(-10, 15) / 100; // ±10-15% yield variation
                $trayData['estimated_yield'] = round($batchConfig['estimated_yield_per_tray'] * (1 + $variation));
                $trayData['yield_unit'] = $batchConfig['yield_unit'] ?? 'grams';
            }

            if (isset($batchConfig['actual_yield_per_tray'])) {
                $variation = rand(-8, 12) / 100; // ±8-12% actual yield variation
                $trayData['actual_yield'] = round($batchConfig['actual_yield_per_tray'] * (1 + $variation));
                $trayData['yield_unit'] = $batchConfig['yield_unit'] ?? 'grams';
            }

            if (isset($batchConfig['quality_grade'])) {
                // Assign mostly A grade with some variation
                $grades = ['A+', 'A', 'A', 'A', 'B+', 'B'];
                $trayData['quality_grade'] = $grades[array_rand($grades)];
            }

            // Add some realistic germination rates
            if ($batchConfig['current_stage_id'] >= 2) {
                $trayData['germination_rate_percent'] = rand(85, 98);
            }

            // Add labor tracking for later stages
            if ($batchConfig['current_stage_id'] >= 4) {
                $trayData['labor_hours'] = round(rand(15, 35) / 10, 1); // 1.5-3.5 hours per tray
            }

            // Environmental conditions (JSON)
            $trayData['environmental_log'] = json_encode([
                'temperature_avg' => rand(65, 75),
                'humidity_avg' => rand(65, 80),
                'ph_level' => round(rand(60, 68) / 10, 1)
            ]);

            Crop::create($trayData);
        }
    }
}