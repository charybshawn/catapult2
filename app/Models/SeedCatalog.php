<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeedCatalog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seed_catalog';

    protected $fillable = [
        'catalog_id',
        'botanical_name',
        'common_name',
        'cultivar',
        'category',
        'display_name',
        'seed_density_oz_per_1020',
        'soak_hours',
        'blackout_days',
        'light_days',
        'total_days',
        'market_tier',
        'flavor_profile',
        'description',
        'seasonal_notes',
        'target_germination_rate',
        'storage_requirements',
        'suppliers',
        'avg_price_per_lb',
        'typical_shelf_life_months',
        'is_active',
        'is_organic_available',
        'growing_tips',
        'image_url',
        'usage_count',
        'last_used_at',
        'success_rate'
    ];

    protected $casts = [
        'seasonal_notes' => 'array',
        'storage_requirements' => 'array',
        'suppliers' => 'array',
        'seed_density_oz_per_1020' => 'decimal:2',
        'target_germination_rate' => 'decimal:1',
        'avg_price_per_lb' => 'decimal:2',
        'success_rate' => 'decimal:1',
        'is_active' => 'boolean',
        'is_organic_available' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    /**
     * Boot method to auto-generate catalog_id and display_name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($seedCatalog) {
            if (empty($seedCatalog->catalog_id)) {
                $seedCatalog->catalog_id = $seedCatalog->generateCatalogId();
            }
            if (empty($seedCatalog->display_name)) {
                $seedCatalog->display_name = $seedCatalog->common_name . ' - ' . $seedCatalog->cultivar;
            }
            if (empty($seedCatalog->total_days)) {
                $seedCatalog->total_days = $seedCatalog->blackout_days + $seedCatalog->light_days;
            }
        });

        static::updating(function ($seedCatalog) {
            if ($seedCatalog->isDirty(['common_name', 'cultivar'])) {
                $seedCatalog->display_name = $seedCatalog->common_name . ' - ' . $seedCatalog->cultivar;
            }
            if ($seedCatalog->isDirty(['blackout_days', 'light_days'])) {
                $seedCatalog->total_days = $seedCatalog->blackout_days + $seedCatalog->light_days;
            }
        });
    }

    /**
     * Generate unique catalog ID based on category, common name, and cultivar
     */
    public function generateCatalogId(): string
    {
        $categoryCode = strtoupper(substr($this->category, 0, 5));
        $nameCode = strtoupper(substr(str_replace(' ', '', $this->common_name), 0, 4));
        $cultivarCode = strtoupper(substr(str_replace(' ', '', $this->cultivar), 0, 4));

        $baseId = $categoryCode . '-' . $nameCode . '-' . $cultivarCode;

        // Ensure uniqueness
        $counter = 1;
        $catalogId = $baseId;
        while (static::where('catalog_id', $catalogId)->exists()) {
            $catalogId = $baseId . '-' . $counter;
            $counter++;
        }

        return $catalogId;
    }

    /**
     * Scope for active seeds only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by market tier
     */
    public function scopeMarketTier($query, $tier)
    {
        return $query->where('market_tier', $tier);
    }

    /**
     * Get recipes that use this seed
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'seed_catalog_id');
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get category options
     */
    public static function getCategories(): array
    {
        return [
            'Brassicas' => 'Brassicas (Cruciferous)',
            'Legumes' => 'Legumes (Peas & Beans)',
            'Herbs' => 'Herbs & Aromatics',
            'Sunflowers' => 'Sunflower Family',
            'Grains' => 'Grains & Grasses',
            'Greens' => 'Leafy Greens',
            'Other' => 'Other Varieties'
        ];
    }

    /**
     * Get market tier options
     */
    public static function getMarketTiers(): array
    {
        return [
            'premium' => 'Premium (High-value specialty)',
            'standard' => 'Standard (Regular production)',
            'volume' => 'Volume (Bulk production)'
        ];
    }

    /**
     * Calculate estimated cost per tray
     */
    public function getEstimatedCostPerTrayAttribute(): ?float
    {
        if ($this->avg_price_per_lb && $this->seed_density_oz_per_1020) {
            $pricePerOz = $this->avg_price_per_lb / 16;
            return round($pricePerOz * $this->seed_density_oz_per_1020, 2);
        }
        return null;
    }

    /**
     * Get growing difficulty level
     */
    public function getGrowingDifficultyAttribute(): string
    {
        $totalDays = $this->total_days;
        $soakHours = $this->soak_hours;

        if ($totalDays <= 7 && $soakHours <= 4) {
            return 'Easy';
        } elseif ($totalDays <= 12 && $soakHours <= 8) {
            return 'Moderate';
        } else {
            return 'Advanced';
        }
    }
}
