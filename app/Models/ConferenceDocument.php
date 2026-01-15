<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConferenceDocument extends Model
{
    use HasFactory;

    protected $table = 'conference_documents';

    protected $fillable = [
        'conference_id',
        'edition_id',
        'document_category',
        'file_name',
        'file_path',
        'display_name',
        'is_active',
        'button_width_percent',
        'display_order',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'button_width_percent' => 'integer',
        'display_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['download_url', 'file_size_formatted', 'is_available'];

    /**
     * Get the conference that owns the document
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the edition that owns the document
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * Get the full download URL
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->is_active && $this->file_path) {
            return Storage::url($this->file_path);
        }

        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Backward compatibility accessor for is_available
     * Maps to is_active field
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope to get active documents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('document_category', $category);
    }
}
