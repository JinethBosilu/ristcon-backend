<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $table = 'committee_members';

    protected $fillable = [
        'conference_id',
        'edition_id',
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
        'photo_path',
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
     * Get the edition that owns the committee member
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get the committee type
     */
    public function committeeType(): BelongsTo
    {
        return $this->belongsTo(CommitteeType::class, 'committee_type_id', 'id');
    }
}
