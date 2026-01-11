<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'edition_id',
        'platform',
        'url',
        'label',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the conference that owns the social media link.
     */
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get the edition that owns the social media link.
     */
    public function edition()
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Scope to only get active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
