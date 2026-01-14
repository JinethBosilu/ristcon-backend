<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInformation extends Model
{
    protected $table = 'payment_information';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'payment_type',
        'beneficiary_name',
        'bank_name',
        'account_number',
        'swift_code',
        'branch_code',
        'branch_name',
        'bank_address',
        'currency',
        'additional_info',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['id'];

    public function getIdAttribute(): int
    {
        return $this->payment_id;
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
