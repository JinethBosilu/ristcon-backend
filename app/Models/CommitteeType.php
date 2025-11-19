<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteeType extends Model
{
    use HasFactory;

    protected $table = 'committee_types';

    protected $fillable = [
        'committee_name',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get all members of this committee type
     */
    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class, 'committee_type_id', 'committee_type_id');
    }
}
