<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * ConferenceEdition Model
 * 
 * Represents a single conference edition/year in the multi-edition system.
 * This model serves as the central hub for all edition-scoped data.
 * 
 * @property int $id
 * @property int $year
 * @property int $edition_number
 * @property string $name
 * @property string $slug
 * @property string $status
 * @property bool $is_active_edition
 * @property Carbon $conference_date
 * @property string $venue_type
 * @property string|null $venue_location
 * @property string $theme
 * @property string|null $description
 * @property string $general_email
 * @property string|null $availability_hours
 * @property int $copyright_year
 * @property string $site_version
 * @property Carbon|null $last_updated
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class ConferenceEdition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conference_editions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year',
        'edition_number',
        'name',
        'slug',
        'status',
        'is_active_edition',
        'conference_date',
        'venue_type',
        'venue_location',
        'theme',
        'description',
        'general_email',
        'availability_hours',
        'copyright_year',
        'site_version',
        'last_updated',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'year' => 'integer',
        'edition_number' => 'integer',
        'is_active_edition' => 'boolean',
        'conference_date' => 'date',
        'copyright_year' => 'integer',
        'last_updated' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'is_upcoming',
        'is_past',
        'days_until_conference',
        'formatted_date',
        'full_name',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get all important dates for this edition.
     */
    public function importantDates(): HasMany
    {
        return $this->hasMany(ImportantDate::class, 'edition_id')
            ->orderBy('display_order')
            ->orderBy('date_value');
    }

    /**
     * Get all speakers for this edition.
     */
    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class, 'edition_id')
            ->orderBy('display_order')
            ->orderBy('full_name');
    }

    /**
     * Get all committee members for this edition.
     */
    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get all contact persons for this edition.
     */
    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get all documents for this edition.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ConferenceDocument::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get all assets for this edition.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(ConferenceAsset::class, 'edition_id');
    }

    /**
     * Get all research categories for this edition.
     */
    public function researchCategories(): HasMany
    {
        return $this->hasMany(ResearchCategory::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get all submission methods for this edition.
     */
    public function submissionMethods(): HasMany
    {
        return $this->hasMany(SubmissionMethod::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get all presentation guidelines for this edition.
     */
    public function presentationGuidelines(): HasMany
    {
        return $this->hasMany(PresentationGuideline::class, 'edition_id');
    }

    /**
     * Get all payment information entries for this edition.
     */
    public function paymentInformation(): HasMany
    {
        return $this->hasMany(PaymentInformation::class, 'edition_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Get all registration fees for this edition.
     */
    public function registrationFees(): HasMany
    {
        return $this->hasMany(RegistrationFee::class, 'edition_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Get all payment policies for this edition.
     */
    public function paymentPolicies(): HasMany
    {
        return $this->hasMany(PaymentPolicy::class, 'edition_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Get all social media links for this edition.
     */
    public function socialMediaLinks(): HasMany
    {
        return $this->hasMany(SocialMediaLink::class, 'edition_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Get all abstract formats for this edition.
     */
    public function abstractFormats(): HasMany
    {
        return $this->hasMany(AbstractFormat::class, 'edition_id')
            ->orderBy('display_order');
    }

    /**
     * Get the event location for this edition.
     */
    public function eventLocation(): HasOne
    {
        return $this->hasOne(EventLocation::class, 'edition_id');
    }

    /**
     * Get the author page configuration for this edition.
     */
    public function authorPageConfig(): HasOne
    {
        return $this->hasOne(AuthorPageConfig::class, 'edition_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get only published editions.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get only draft editions.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get only archived editions.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope to get the active edition (default for API).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active_edition', true);
    }

    /**
     * Scope to get edition by year.
     */
    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get upcoming conferences.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('conference_date', '>=', now()->startOfDay());
    }

    /**
     * Scope to get past conferences.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('conference_date', '<', now()->startOfDay());
    }

    /**
     * Scope to order by year descending (newest first).
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('year', 'desc');
    }

    /**
     * Scope to order by year ascending (oldest first).
     */
    public function scopeOldestFirst(Builder $query): Builder
    {
        return $query->orderBy('year', 'asc');
    }

    // ==================== ACCESSORS ====================

    /**
     * Check if the conference is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->conference_date >= now()->startOfDay();
    }

    /**
     * Check if the conference is past.
     */
    public function getIsPastAttribute(): bool
    {
        return $this->conference_date < now()->startOfDay();
    }

    /**
     * Get days until conference (negative if past).
     */
    public function getDaysUntilConferenceAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->conference_date, false);
    }

    /**
     * Get formatted conference date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->conference_date->format('F j, Y');
    }

    /**
     * Get full conference name with edition number.
     */
    public function getFullNameAttribute(): string
    {
        $ordinal = $this->getOrdinalSuffix($this->edition_number);
        return "{$this->edition_number}{$ordinal} {$this->name}";
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get ordinal suffix for edition number.
     */
    private function getOrdinalSuffix(int $number): string
    {
        $lastDigit = $number % 10;
        $lastTwoDigits = $number % 100;

        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 13) {
            return 'th';
        }

        return match ($lastDigit) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    }

    /**
     * Check if this edition can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Don't allow deletion of active edition
        if ($this->is_active_edition) {
            return false;
        }

        // Don't allow deletion of published editions (must be draft, archived, or cancelled)
        if ($this->status === 'published') {
            return false;
        }

        // Allow deletion of draft, archived, and cancelled editions
        return true;
    }

    /**
     * Mark this edition as the active edition.
     * Automatically deactivates all other editions.
     */
    public function markAsActive(): bool
    {
        // Deactivate all other editions
        static::where('id', '!=', $this->id)
            ->where('is_active_edition', true)
            ->update(['is_active_edition' => false]);

        // Activate this edition
        $this->is_active_edition = true;
        return $this->save();
    }

    /**
     * Archive this edition.
     */
    public function archive(): bool
    {
        if ($this->is_active_edition) {
            return false; // Cannot archive active edition
        }

        $this->status = 'archived';
        return $this->save();
    }

    /**
     * Publish this edition.
     */
    public function publish(): bool
    {
        $this->status = 'published';
        return $this->save();
    }

    /**
     * Set this edition as draft (unpublish).
     */
    public function draft(): bool
    {
        if ($this->is_active_edition) {
            return false; // Cannot set active edition as draft
        }

        $this->status = 'draft';
        return $this->save();
    }

    // ==================== EVENTS ====================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one active edition at a time
        static::creating(function ($edition) {
            if ($edition->is_active_edition) {
                static::where('is_active_edition', true)
                    ->update(['is_active_edition' => false]);
            }
        });

        static::updating(function ($edition) {
            if ($edition->isDirty('is_active_edition') && $edition->is_active_edition) {
                static::where('id', '!=', $edition->id)
                    ->where('is_active_edition', true)
                    ->update(['is_active_edition' => false]);
            }
        });
    }
}
