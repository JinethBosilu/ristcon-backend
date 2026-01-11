<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentationGuideline extends Model
{
    use HasFactory;

    protected $table = 'presentation_guidelines';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'presentation_type',
        'duration_minutes',
        'presentation_minutes',
        'qa_minutes',
        'poster_width',
        'poster_height',
        'poster_unit',
        'poster_orientation',
        'physical_presence_required',
        'detailed_requirements',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'presentation_minutes' => 'integer',
        'qa_minutes' => 'integer',
        'poster_width' => 'decimal:2',
        'poster_height' => 'decimal:2',
        'physical_presence_required' => 'boolean',
    ];

    /**
     * Get the conference that owns the guideline
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the guideline
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get formatted poster dimensions
     */
    public function getPosterDimensionsAttribute(): ?string
    {
        if ($this->poster_width && $this->poster_height) {
            return "{$this->poster_width}{$this->poster_unit} × {$this->poster_height}{$this->poster_unit} ({$this->poster_orientation})";
        }

        return null;
    }

    /**
     * Get formatted duration for oral presentations
     */
    public function getDurationFormattedAttribute(): ?string
    {
        if ($this->presentation_type === 'oral' && $this->duration_minutes) {
            return "{$this->duration_minutes} minutes ({$this->presentation_minutes} presentation + {$this->qa_minutes} Q&A)";
        }

        return null;
    }
}
