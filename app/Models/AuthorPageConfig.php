<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorPageConfig extends Model
{
    use HasFactory;

    protected $table = 'author_page_config';

    protected $fillable = [
        'conference_id',
        'conference_format',
        'cmt_url',
        'submission_email',
        'blind_review_enabled',
        'camera_ready_required',
        'special_instructions',
        'acknowledgment_text',
    ];

    protected $casts = [
        'blind_review_enabled' => 'boolean',
        'camera_ready_required' => 'boolean',
    ];

    /**
     * Get the conference that owns the configuration
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }
}
