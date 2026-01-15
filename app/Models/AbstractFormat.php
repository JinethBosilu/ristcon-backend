<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbstractFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'edition_id',
        'format_type',
        'max_title_characters',
        'title_font_name',
        'title_font_size',
        'title_style',
        'max_body_words',
        'body_font_name',
        'body_font_size',
        'body_line_spacing',
        'max_keywords',
        'keywords_font_name',
        'keywords_font_size',
        'keywords_style',
        'max_references',
        'sections',
        'additional_notes',
        'display_order',
    ];

    protected $casts = [
        'max_title_characters' => 'integer',
        'title_font_size' => 'integer',
        'max_body_words' => 'integer',
        'body_font_size' => 'integer',
        'body_line_spacing' => 'decimal:1',
        'max_keywords' => 'integer',
        'keywords_font_size' => 'integer',
        'max_references' => 'integer',
        'sections' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Get the conference that owns the abstract format
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get the edition that owns the abstract format
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Scope to filter by format type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('format_type', $type);
    }
}
