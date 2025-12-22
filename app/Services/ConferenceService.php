<?php

namespace App\Services;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ConferenceService
{
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
     * Build query with filters and relations
     */
    public function buildQuery(array $filters = [], ?string $includeString = null): Builder
    {
        $query = Conference::query();

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
     */
    public function getAllConferences(array $filters = [], ?string $includeString = null): Collection
    {
        return $this->buildQuery($filters, $includeString)
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Get conference by year
     */
    public function getConferenceByYear(int $year, ?string $includeString = null): ?Conference
    {
        $query = Conference::where('year', $year);

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
     * Get speakers for a conference
     */
    public function getConferenceSpeakers(Conference $conference, ?string $type = null): Collection
    {
        $query = $conference->speakers();

        if ($type) {
            $query->where('speaker_type', $type);
        }

        return $query->get();
    }

    /**
     * Get important dates for a conference
     */
    public function getConferenceDates(Conference $conference): Collection
    {
        return $conference->importantDates;
    }

    /**
     * Get committee members for a conference
     */
    public function getConferenceCommittees(Conference $conference, ?string $type = null): Collection
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
     * Get contact persons for a conference
     */
    public function getConferenceContacts(Conference $conference): Collection
    {
        return $conference->contactPersons;
    }

    /**
     * Get documents for a conference
     */
    public function getConferenceDocuments(Conference $conference, ?string $category = null, ?bool $active = null): Collection
    {
        $query = $conference->documents();

        if ($category) {
            $query->byCategory($category);
        }

        if ($active !== null) {
            $query->where('is_active', $active);
        }

        return $query->get();
    }

    /**
     * Get research areas for a conference
     */
    public function getConferenceResearchAreas(Conference $conference): Collection
    {
        return $conference->researchCategories()
            ->with('researchAreas')
            ->active()
            ->get();
    }

    /**
     * Get event location for a conference
     */
    public function getConferenceLocation(Conference $conference)
    {
        return $conference->eventLocation;
    }

    /**
     * Get author instructions for a conference
     */
    public function getAuthorInstructions(int $year): array
    {
        $conference = Conference::where('year', $year)
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
