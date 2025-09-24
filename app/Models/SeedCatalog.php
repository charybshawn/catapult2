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
        'common_name',
        'cultivars',
        'category',
        'is_active',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'cultivars' => 'array',
        'is_active' => 'boolean',
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
        });
    }

    /**
     * Generate unique catalog ID based on category and common name only
     */
    public function generateCatalogId(): string
    {
        $categoryCode = strtoupper(substr($this->category, 0, 5));
        $nameCode = strtoupper(substr(str_replace(' ', '', $this->common_name), 0, 8));

        $baseId = $categoryCode . '-' . $nameCode;

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
     * Get display name (computed attribute)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->common_name;
    }

    /**
     * Get cultivar list formatted for display
     */
    public function getCultivarListAttribute(): string
    {
        if (!$this->cultivars || count($this->cultivars) === 0) {
            return 'No cultivars';
        }

        return implode(', ', $this->cultivars);
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


}
