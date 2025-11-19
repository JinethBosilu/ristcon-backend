<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchArea extends Model
{
    use HasFactory;

    protected $table = 'research_areas';

    protected $fillable = [
        'category_id',
        'area_name',
        'alternate_names',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'alternate_names' => 'array',
    ];

    /**
     * Get the category that owns the research area
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ResearchCategory::class, 'category_id');
    }

    /**
     * Scope to get active research areas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
