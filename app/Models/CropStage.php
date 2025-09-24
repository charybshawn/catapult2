<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropStage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'color_code',
        'icon',
        'requires_light',
        'requires_watering',
        'can_harvest',
        'is_terminal',
        'default_temperature_range',
        'default_humidity_range',
        'default_duration_days',
        'is_active'
    ];

    protected $casts = [
        'requires_light' => 'boolean',
        'requires_watering' => 'boolean',
        'can_harvest' => 'boolean',
        'is_terminal' => 'boolean',
        'is_active' => 'boolean',
        'default_temperature_range' => 'array',
        'default_humidity_range' => 'array'
    ];

    /**
     * Get crops in this stage
     */
    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class, 'current_stage_id');
    }

    /**
     * Scope for active stages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for stages ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}