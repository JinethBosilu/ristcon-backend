<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLocation extends Model
{
    use HasFactory;

    protected $table = 'event_locations';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'venue_name',
        'full_address',
        'city',
        'country',
        'latitude',
        'longitude',
        'google_maps_embed_url',
        'google_maps_link',
        'is_virtual',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_virtual' => 'boolean',
    ];

    /**
     * Get the conference that owns the location
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the location
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get Google Maps link from coordinates
     */
    public function getGoogleMapsDirectionLinkAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/dir//{$this->latitude},{$this->longitude}";
        }

        return null;
    }
}
