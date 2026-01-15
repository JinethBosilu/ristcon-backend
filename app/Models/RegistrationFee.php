<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationFee extends Model
{
    protected $table = 'registration_fees';
    protected $primaryKey = 'fee_id';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'attendee_type',
        'currency',
        'amount',
        'early_bird_amount',
        'early_bird_deadline',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'early_bird_amount' => 'decimal:2',
        'early_bird_deadline' => 'date',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['id'];

    /**
     * Get the id attribute (alias for fee_id for API compatibility)
     */
    public function getIdAttribute()
    {
        return $this->fee_id;
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }
}
