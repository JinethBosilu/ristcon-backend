<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConferenceAsset extends Model
{
    use HasFactory;

    protected $table = 'conference_assets';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'asset_type',
        'file_name',
        'file_path',
        'alt_text',
        'usage_context',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = ['asset_url'];

    /**
     * Get the conference that owns the asset
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the asset
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get the full URL for the asset
     */
    public function getAssetUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return null;
    }

    /**
     * Scope to filter by asset type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('asset_type', $type);
    }
}
