<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Speaker extends Model
{
    use HasFactory;

    protected $table = 'speakers';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'speaker_type',
        'display_order',
        'full_name',
        'title',
        'affiliation',
        'additional_affiliation',
        'bio',
        'photo_filename',
        'website_url',
        'email',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    protected $appends = ['photo_url'];

    /**
     * Get the conference that owns the speaker
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    /**
     * Get the edition that owns the speaker
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get the full URL for the speaker's photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_filename) {
            return Storage::url('speakers/' . $this->photo_filename);
        }

        return null;
    }

    /**
     * Scope to get keynote speakers
     */
    public function scopeKeynote($query)
    {
        return $query->where('speaker_type', 'keynote');
    }

    /**
     * Scope to get plenary speakers
     */
    public function scopePlenary($query)
    {
        return $query->where('speaker_type', 'plenary');
    }
}
