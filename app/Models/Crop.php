<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'batch_number',
        'crop_batch',
        'tray_id',
        'tray_number',
        'tray_type',
        'current_stage_id',
        'recipe_id',
        'variety',
        'tray_size',
        'tray_count',
        'location',
        'position_x',
        'position_y',
        'seed_lot_number',
        'seed_weight_grams',
        'current_stage',
        'stage_started_at',
        'planted_at',
        'soak_started_at',
        'germination_started_at',
        'blackout_started_at',
        'light_started_at',
        'ready_at',
        'harvested_at',
        'expected_harvest_date',
        'actual_harvest_date',
        'harvested_weight_grams',
        'yield_percentage',
        'yield_unit',
        'quality_grade',
        'notes',
        'status',
        'failure_reason',
        'created_by',
        'environmental_log',
        'total_water_ml',
        'has_contamination',
        'is_quarantined'
    ];

    protected $casts = [
        'stage_started_at' => 'datetime',
        'planted_at' => 'datetime',
        'expected_harvest_date' => 'datetime',
        'actual_harvest_date' => 'datetime',
        'soak_started_at' => 'datetime',
        'germination_started_at' => 'datetime',
        'blackout_started_at' => 'datetime',
        'light_started_at' => 'datetime',
        'ready_at' => 'datetime',
        'harvested_at' => 'datetime',
        'packed_at' => 'datetime',
        'environmental_log' => 'array',
        'quality_metrics' => 'array',
        'has_contamination' => 'boolean',
        'is_quarantined' => 'boolean'
    ];

    // Crop stages
    const STAGE_PLANTED = 'planted';
    const STAGE_SOAKING = 'soaking';
    const STAGE_GERMINATION = 'germination';
    const STAGE_BLACKOUT = 'blackout';
    const STAGE_LIGHT = 'light';
    const STAGE_HARVESTED = 'harvested';

    // Crop statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_HARVESTED = 'harvested';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStages(): array
    {
        return [
            self::STAGE_PLANTED => 'Planted',
            self::STAGE_SOAKING => 'Soaking',
            self::STAGE_GERMINATION => 'Germination',
            self::STAGE_BLACKOUT => 'Blackout',
            self::STAGE_LIGHT => 'Light',
            self::STAGE_HARVESTED => 'Harvested'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_HARVESTED => 'Harvested',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    /**
     * Get the current stage for the crop
     */
    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(CropStage::class, 'current_stage_id');
    }

    /**
     * Get the recipe for the crop
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the user who created the crop
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the harvests for the crop (disabled - model doesn't exist)
     */
    // public function harvests(): HasMany
    // {
    //     return $this->hasMany(Harvest::class);
    // }

    /**
     * Get the tasks for the crop (disabled - model doesn't exist)
     */
    // public function tasks(): HasMany
    // {
    //     return $this->hasMany(Task::class);
    // }

    /**
     * Check if crop is overdue based on expected harvest date
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->expected_harvest_date
            && $this->expected_harvest_date->isPast();
    }

    /**
     * Get days since planting
     */
    public function getDaysInProductionAttribute(): int
    {
        if (!$this->planted_at) {
            return 0;
        }

        $endDate = $this->actual_harvest_date ?? now();
        return $this->planted_at->diffInDays($endDate);
    }

    /**
     * Get the current stage based on datetime columns
     */
    public function getCurrentStageAttribute(): string
    {
        if ($this->harvested_at) return self::STAGE_HARVESTED;
        if ($this->light_started_at) return self::STAGE_LIGHT;
        if ($this->blackout_started_at) return self::STAGE_BLACKOUT;
        if ($this->germination_started_at) return self::STAGE_GERMINATION;
        if ($this->soak_started_at) return self::STAGE_SOAKING;
        return self::STAGE_PLANTED;
    }

    /**
     * Get stage color for UI
     */
    public function getStageColorAttribute(): string
    {
        return match($this->current_stage) {
            self::STAGE_PLANTED, self::STAGE_SOAKING => 'blue',
            self::STAGE_GERMINATION => 'indigo',
            self::STAGE_BLACKOUT => 'gray',
            self::STAGE_LIGHT => 'yellow',
            self::STAGE_HARVESTED => 'emerald',
            default => 'slate'
        };
    }

    /**
     * Advance to next stage using individual datetime columns
     */
    public function advanceStage($stageChangeDate = null): void
    {
        $changeDate = $stageChangeDate ? \Carbon\Carbon::parse($stageChangeDate) : now();
        $currentStage = $this->current_stage;

        switch ($currentStage) {
            case self::STAGE_PLANTED:
                $this->soak_started_at = $changeDate;
                break;
            case self::STAGE_SOAKING:
                $this->germination_started_at = $changeDate;
                break;
            case self::STAGE_GERMINATION:
                $this->blackout_started_at = $changeDate;
                break;
            case self::STAGE_BLACKOUT:
                $this->light_started_at = $changeDate;
                break;
            case self::STAGE_LIGHT:
                $this->harvested_at = $changeDate;
                $this->status = 'harvested';
                break;
            default:
                // Already at final stage or invalid stage
                return;
        }

        $this->save();
    }

    /**
     * Scope for active crops
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for crops ready to harvest
     */
    public function scopeHarvestReady($query)
    {
        return $query->whereNotNull('light_started_at')
                     ->whereNull('harvested_at')
                     ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get all trays in the same batch
     */
    public function batchTrays()
    {
        return self::where('crop_batch', $this->crop_batch);
    }

    /**
     * Get batch summary statistics
     */
    public function getBatchSummaryAttribute()
    {
        $batchTrays = $this->batchTrays()->get();

        return [
            'total_trays' => $batchTrays->count(),
            'active_trays' => $batchTrays->where('status', self::STATUS_ACTIVE)->count(),
            'harvested_trays' => $batchTrays->where('status', self::STATUS_HARVESTED)->count(),
            'failed_trays' => $batchTrays->where('status', self::STATUS_FAILED)->count(),
        ];
    }

    /**
     * Generate next tray ID for a batch
     */
    public static function generateTrayId($batchCode, $number)
    {
        // Example: 1-A1 where A1 is the tray position
        $row = chr(65 + floor(($number - 1) / 10)); // A, B, C, etc.
        $col = (($number - 1) % 10) + 1; // 1-10
        return "{$batchCode}-{$row}{$col}";
    }

    /**
     * Generate next batch code as a simple incrementing number
     *
     * @return string The next batch code (e.g., "1", "2", "3")
     */
    public static function generateNextBatchCode(): string
    {
        // Get all batch codes including soft-deleted records and find the highest numeric one in PHP
        $allBatches = self::select('crop_batch')
            ->withTrashed() // Include soft-deleted records to prevent reusing batch codes
            ->distinct()
            ->pluck('crop_batch')
            ->filter(function ($batch) {
                return is_numeric($batch);
            })
            ->map(function ($batch) {
                return intval($batch);
            });

        $nextNumber = $allBatches->isEmpty() ? 1 : $allBatches->max() + 1;

        return (string) $nextNumber;
    }



    /**
     * Validate batch code format
     *
     * @param string $batchCode The batch code to validate
     * @return bool True if valid, false otherwise
     */
    public static function isValidBatchCodeFormat(string $batchCode): bool
    {
        return is_numeric($batchCode) && intval($batchCode) > 0;
    }

    // Test method without PHPDoc to trigger auditor
    public function testMethod()
    {
        return 'testing';
    }
}
