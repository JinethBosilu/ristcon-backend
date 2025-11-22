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
        'attendee_type',
        'currency',
        'amount',
        'early_bird_amount',
        'early_bird_deadline',
        'display_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'early_bird_amount' => 'decimal:2',
        'early_bird_deadline' => 'date',
        'display_order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }
}
