<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ConferenceDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conference_documents';

    protected $fillable = [
        'conference_id',
        'document_category',
        'file_name',
        'file_path',
        'display_name',
        'is_available',
        'button_width_percent',
        'display_order',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'button_width_percent' => 'integer',
        'display_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['download_url', 'file_size_formatted'];

    /**
     * Get the conference that owns the document
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }

    /**
     * Get the full download URL
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->is_available && $this->file_path) {
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
     * Scope to get available documents
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('document_category', $category);
    }
}
