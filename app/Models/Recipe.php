<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'variety',
        'category',
        'days_to_maturity',
        'seed_density_grams_per_tray',
        'soak_hours',
        'germination_days',
        'blackout_days',
        'light_days',
        'estimated_yield_grams',
        'instructions',
        'temperature_min',
        'temperature_max',
        'humidity_min',
        'humidity_max',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'instructions' => 'array'
    ];

    /**
     * Get all crops using this recipe
     */
    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class);
    }

    /**
     * Scope for active recipes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get total estimated days
     */
    public function getTotalDaysAttribute(): int
    {
        return $this->germination_days + $this->blackout_days + $this->light_days;
    }
}