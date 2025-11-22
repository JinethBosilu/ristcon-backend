<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPolicy extends Model
{
    protected $table = 'payment_policies';
    protected $primaryKey = 'policy_id';

    protected $fillable = [
        'conference_id',
        'policy_text',
        'policy_type',
        'is_highlighted',
        'display_order',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
        'display_order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }
}
