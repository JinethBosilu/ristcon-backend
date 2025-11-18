<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_persons';
    protected $primaryKey = 'contact_id';

    protected $fillable = [
        'conference_id',
        'full_name',
        'role',
        'department',
        'mobile',
        'phone',
        'email',
        'address',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get the conference that owns the contact person
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
}
