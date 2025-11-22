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
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }
}
