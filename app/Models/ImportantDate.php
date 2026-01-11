<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantDate extends Model
{
    use HasFactory;

    protected $table = 'important_dates';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'date_type',
        'date_value',
        'is_extended',
        'original_date',
        'display_order',
        'display_label',
        'notes',
    ];

    protected $casts = [
        'date_value' => 'date',
        'original_date' => 'date',
        'is_extended' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the conference that owns the important date
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the important date
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Check if the date has passed
     */
    public function getIsPassedAttribute(): bool
    {
        return $this->date_value->isPast();
    }

    /**
     * Get days remaining until this date
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->is_passed) {
            return null;
        }

        return now()->diffInDays($this->date_value, false);
    }
}
