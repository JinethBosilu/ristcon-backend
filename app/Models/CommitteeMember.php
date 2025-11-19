<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'committee_members';

    protected $fillable = [
        'conference_id',
        'committee_type_id',
        'full_name',
        'designation',
        'department',
        'affiliation',
        'role',
        'role_category',
        'country',
        'is_international',
        'display_order',
    ];

    protected $casts = [
        'is_international' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the conference that owns the committee member
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the committee type
     */
    public function committeeType(): BelongsTo
    {
        return $this->belongsTo(CommitteeType::class, 'committee_type_id', 'id');
    }
}
