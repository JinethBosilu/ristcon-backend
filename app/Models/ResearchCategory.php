<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchCategory extends Model
{
    use HasFactory;

    protected $table = 'research_categories';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'category_code',
        'category_name',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the conference that owns the category
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the category
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get research areas under this category
     */
    public function researchAreas(): HasMany
    {
        return $this->hasMany(ResearchArea::class, 'category_id')
            ->orderBy('display_order');
    }

    /**
     * Scope to get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
