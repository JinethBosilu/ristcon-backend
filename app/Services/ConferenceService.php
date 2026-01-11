<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\ConferenceEdition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ConferenceService
{
    /**
     * Edition service for resolving conference editions
     */
    protected EditionService $editionService;

    /**
     * Constructor
     */
    public function __construct(EditionService $editionService)
    {
        $this->editionService = $editionService;
    }
    /**
     * Relation mapping for API includes
     */
    protected const RELATION_MAP = [
        'speakers' => 'speakers',
        'important_dates' => 'importantDates',
        'committees' => 'committeeMembers.committeeType',
        'documents' => 'documents',
        'assets' => 'assets',
        'location' => 'eventLocation',
        'research_areas' => 'researchCategories.researchAreas',
        'author_config' => 'authorPageConfig',
        'submission_methods' => 'submissionMethods',
        'presentation_guidelines' => 'presentationGuidelines',
        'contacts' => 'contactPersons',
        'social_media' => 'socialMediaLinks',
        'abstract_formats' => 'abstractFormats',
    ];

    /**
     * Build query with filters and relations for ConferenceEdition
     */
    public function buildQuery(array $filters = [], ?string $includeString = null): Builder
    {
        $query = ConferenceEdition::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        // Apply relations
        if ($includeString) {
            $includes = explode(',', $includeString);
            foreach ($includes as $include) {
                $include = trim($include);
                if (isset(self::RELATION_MAP[$include])) {
                    $query->with(self::RELATION_MAP[$include]);
                }
            }
        }

        return $query;
    }

    /**
     * Get all conferences with optional filters
     * Returns ConferenceEdition objects formatted as Conference for backward compatibility
     */
    public function getAllConferences(array $filters = [], ?string $includeString = null): Collection
    {
        return $this->buildQuery($filters, $includeString)
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Get conference by year
     * Returns ConferenceEdition formatted as Conference for backward compatibility
     */
    public function getConferenceByYear(int $year, ?string $includeString = null): ?ConferenceEdition
    {
        $query = ConferenceEdition::where('year', $year);

        if ($includeString) {
            $includes = explode(',', $includeString);
            foreach ($includes as $include) {
                $include = trim($include);
                if (isset(self::RELATION_MAP[$include])) {
                    $query->with(self::RELATION_MAP[$include]);
                }
            }
        }

        return $query->first();
    }

    /**
     * Get speakers for a conference edition
     */
    public function getConferenceSpeakers($conference, ?string $type = null): Collection
    {
        $query = $conference->speakers();

        if ($type) {
            $query->where('speaker_type', $type);
        }

        return $query->get();
    }

    /**
     * Get important dates for a conference edition
     */
    public function getConferenceDates($conference): SupportCollection
    {
        $today = Carbon::now()->startOfDay();
        
        return $conference->importantDates->map(function ($date) use ($today, $conference) {
            $dateValue = Carbon::parse($date->date_value)->startOfDay();
            $daysRemaining = $today->diffInDays($dateValue, false);
            
            return [
                'id' => $date->id,
                'conference_id' => $date->conference_id ?? $conference->id, // Backward compatibility
                'date_type' => $date->date_type,
                'date_value' => $date->date_value,
                'is_extended' => $date->is_extended,
                'display_order' => $date->display_order,
                'display_label' => $date->display_label,
                'notes' => $date->notes,
                'created_at' => $date->created_at,
                'updated_at' => $date->updated_at,
                // Computed fields for frontend
                'is_passed' => $dateValue->isPast(),
                'days_remaining' => (int) $daysRemaining,
                'original_date' => null, // This would come from a separate field if tracking extensions
            ];
        });
    }

    /**
     * Get committee members for a conference edition
     */
    public function getConferenceCommittees($conference, ?string $type = null): Collection
    {
        $query = $conference->committeeMembers()->with('committeeType');

        if ($type) {
            $query->whereHas('committeeType', function ($q) use ($type) {
                $q->where('committee_name', $type);
            });
        }

        return $query->get();
    }

    /**
     * Get contact persons for a conference edition
     */
    public function getConferenceContacts($conference): Collection
    {
        return $conference->contactPersons;
    }

    /**
     * Get documents for a conference edition
     */
    public function getConferenceDocuments($conference, ?string $category = null, ?bool $active = null): Collection
    {
        $query = $conference->documents();

        if ($category) {
            $query->where('document_category', $category);
        }

        if ($active !== null) {
            $query->where('is_active', $active);
        }

        return $query->get();
    }

    /**
     * Get research areas for a conference edition
     */
    public function getConferenceResearchAreas($conference): Collection
    {
        return $conference->researchCategories()
            ->with('researchAreas')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get event location for a conference edition
     */
    public function getConferenceLocation($conference)
    {
        return $conference->eventLocation;
    }

    /**
     * Get author instructions for a conference by year
     */
    public function getAuthorInstructions(int $year): array
    {
        $conference = ConferenceEdition::where('year', $year)
            ->with([
                'authorPageConfig',
                'submissionMethods',
                'presentationGuidelines'
            ])
            ->first();

        if (!$conference) {
            return [];
        }

        return [
            'config' => $conference->authorPageConfig,
            'submission_methods' => $conference->submissionMethods,
            'presentation_guidelines' => $conference->presentationGuidelines,
        ];
    }
}
