<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conference extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conferences';

    protected $fillable = [
        'year',
        'edition_number',
        'conference_date',
        'venue_type',
        'venue_location',
        'theme',
        'description',
        'status',
        'general_email',
        'availability_hours',
        'last_updated',
        'copyright_year',
        'site_version',
    ];

    protected $casts = [
        'conference_date' => 'date',
        'last_updated' => 'datetime',
        'year' => 'integer',
        'edition_number' => 'integer',
        'copyright_year' => 'integer',
    ];

    /**
     * Get important dates for the conference
     */
    public function importantDates(): HasMany
    {
        return $this->hasMany(ImportantDate::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get speakers for the conference
     */
    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get committee members for the conference
     */
    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class, 'conference_id');
    }

    /**
     * Get contact persons for the conference
     */
    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get documents for the conference
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ConferenceDocument::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get assets for the conference
     */
    public function assets(): HasMany
    {
        return $this->hasMany(ConferenceAsset::class, 'conference_id');
    }

    /**
     * Get research categories for the conference
     */
    public function researchCategories(): HasMany
    {
        return $this->hasMany(ResearchCategory::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get event location for the conference
     */
    public function eventLocation(): HasOne
    {
        return $this->hasOne(EventLocation::class, 'conference_id');
    }

    /**
     * Get author page configuration
     */
    public function authorPageConfig(): HasOne
    {
        return $this->hasOne(AuthorPageConfig::class, 'conference_id');
    }

    /**
     * Get submission methods for the conference
     */
    public function submissionMethods(): HasMany
    {
        return $this->hasMany(SubmissionMethod::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get presentation guidelines for the conference
     */
    public function presentationGuidelines(): HasMany
    {
        return $this->hasMany(PresentationGuideline::class, 'conference_id');
    }

    /**
     * Get payment information for the conference
     */
    public function paymentInformation(): HasMany
    {
        return $this->hasMany(PaymentInformation::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get registration fees for the conference
     */
    public function registrationFees(): HasMany
    {
        return $this->hasMany(RegistrationFee::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get payment policies for the conference
     */
    public function paymentPolicies(): HasMany
    {
        return $this->hasMany(PaymentPolicy::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get social media links for the conference
     */
    public function socialMediaLinks(): HasMany
    {
        return $this->hasMany(SocialMediaLink::class, 'conference_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Scope to get upcoming conferences
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope to get completed conferences
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get abstract formats for the conference
     */
    public function abstractFormats(): HasMany
    {
        return $this->hasMany(AbstractFormat::class, 'conference_id')
            ->orderBy('display_order');
    }

    /**
     * Get countdown data for the conference
     */
    public function getCountdownAttribute()
    {
        if ($this->conference_date) {
            $now = now();
            $conferenceDate = $this->conference_date->startOfDay();
            
            if ($conferenceDate->isPast()) {
                return null;
            }

            $diff = $now->diff($conferenceDate);
            
            return [
                'days' => $diff->days,
                'hours' => $diff->h,
                'minutes' => $diff->i,
                'seconds' => $diff->s,
                'target_date' => $conferenceDate->toIso8601String(),
            ];
        }

        return null;
    }
}
