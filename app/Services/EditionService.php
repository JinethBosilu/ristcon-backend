<?php

namespace App\Services;

use App\Models\ConferenceEdition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * EditionService
 * 
 * Central service for managing conference edition resolution and queries.
 * Handles backward compatibility with year-based API parameters.
 */
class EditionService
{
    /**
     * Cache key for active edition
     */
    const ACTIVE_EDITION_CACHE_KEY = 'conference:active_edition';

    /**
     * Cache TTL in seconds (1 hour)
     */
    const CACHE_TTL = 3600;

    /**
     * Get the active edition (default edition when no year is specified).
     * 
     * @return ConferenceEdition|null
     */
    public function getActiveEdition(): ?ConferenceEdition
    {
        return Cache::remember(
            self::ACTIVE_EDITION_CACHE_KEY,
            self::CACHE_TTL,
            function () {
                return ConferenceEdition::where('is_active_edition', true)
                    ->where('status', '!=', 'cancelled')
                    ->first();
            }
        );
    }

    /**
     * Get edition by year.
     * 
     * @param int $year
     * @return ConferenceEdition|null
     */
    public function getEditionByYear(int $year): ?ConferenceEdition
    {
        return Cache::remember(
            "conference:edition:{$year}",
            self::CACHE_TTL,
            function () use ($year) {
                return ConferenceEdition::where('year', $year)
                    ->first();
            }
        );
    }

    /**
     * Get edition by slug.
     * 
     * @param string $slug
     * @return ConferenceEdition|null
     */
    public function getEditionBySlug(string $slug): ?ConferenceEdition
    {
        return Cache::remember(
            "conference:edition:slug:{$slug}",
            self::CACHE_TTL,
            function () use ($slug) {
                return ConferenceEdition::where('slug', $slug)
                    ->first();
            }
        );
    }

    /**
     * Get edition by ID.
     * 
     * @param int $id
     * @return ConferenceEdition|null
     */
    public function getEditionById(int $id): ?ConferenceEdition
    {
        return ConferenceEdition::find($id);
    }

    /**
     * Resolve edition from year parameter or return active edition.
     * 
     * @param int|null $year
     * @return ConferenceEdition|null
     */
    public function resolveEdition(?int $year = null): ?ConferenceEdition
    {
        if ($year !== null) {
            return $this->getEditionByYear($year);
        }

        return $this->getActiveEdition();
    }

    /**
     * Get all published editions ordered by year descending.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedEditions()
    {
        return ConferenceEdition::published()
            ->latestFirst()
            ->get();
    }

    /**
     * Get all editions with optional filters.
     * 
     * @param array $filters ['status' => 'published', 'year' => 2026]
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEditions(array $filters = [])
    {
        $query = ConferenceEdition::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active_edition', $filters['is_active']);
        }

        return $query->latestFirst()->get();
    }

    /**
     * Check if an edition exists for a given year.
     * 
     * @param int $year
     * @return bool
     */
    public function editionExists(int $year): bool
    {
        return ConferenceEdition::where('year', $year)->exists();
    }

    /**
     * Get upcoming editions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingEditions()
    {
        return ConferenceEdition::published()
            ->upcoming()
            ->latestFirst()
            ->get();
    }

    /**
     * Get past (archived) editions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPastEditions()
    {
        return ConferenceEdition::whereIn('status', ['archived', 'published'])
            ->past()
            ->latestFirst()
            ->get();
    }

    /**
     * Mark an edition as active (deactivates all others).
     * 
     * @param int $editionId
     * @return bool
     */
    public function setActiveEdition(int $editionId): bool
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition) {
            return false;
        }

        $result = $edition->markAsActive();

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Archive an edition.
     * 
     * @param int $editionId
     * @return bool
     */
    public function archiveEdition(int $editionId): bool
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition) {
            return false;
        }

        $result = $edition->archive();

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Publish an edition.
     * 
     * @param int $editionId
     * @return bool
     */
    public function publishEdition(int $editionId): bool
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition) {
            return false;
        }

        $result = $edition->publish();

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Create a new edition.
     * 
     * @param array $data
     * @return ConferenceEdition
     */
    public function createEdition(array $data): ConferenceEdition
    {
        $edition = ConferenceEdition::create($data);

        $this->clearCache();

        return $edition;
    }

    /**
     * Update an edition.
     * 
     * @param int $editionId
     * @param array $data
     * @return bool
     */
    public function updateEdition(int $editionId, array $data): bool
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition) {
            return false;
        }

        $result = $edition->update($data);

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Delete an edition (soft delete).
     * 
     * @param int $editionId
     * @return bool
     */
    public function deleteEdition(int $editionId): bool
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition || !$edition->canBeDeleted()) {
            return false;
        }

        $result = $edition->delete();

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Clear all edition-related caches.
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::ACTIVE_EDITION_CACHE_KEY);
        
        // Clear year-based caches
        $editions = ConferenceEdition::all();
        foreach ($editions as $edition) {
            Cache::forget("conference:edition:{$edition->year}");
            Cache::forget("conference:edition:slug:{$edition->slug}");
        }
    }

    /**
     * Get edition statistics.
     * 
     * @param int $editionId
     * @return array
     */
    public function getEditionStatistics(int $editionId): array
    {
        $edition = $this->getEditionById($editionId);

        if (!$edition) {
            return [];
        }

        return [
            'speakers_count' => $edition->speakers()->count(),
            'committee_members_count' => $edition->committeeMembers()->count(),
            'documents_count' => $edition->documents()->count(),
            'research_categories_count' => $edition->researchCategories()->count(),
            'registration_fees_count' => $edition->registrationFees()->count(),
            'important_dates_count' => $edition->importantDates()->count(),
            'contact_persons_count' => $edition->contactPersons()->count(),
            'has_location' => $edition->eventLocation()->exists(),
            'has_author_config' => $edition->authorPageConfig()->exists(),
            'status' => $edition->status,
            'is_active' => $edition->is_active_edition,
            'is_upcoming' => $edition->is_upcoming,
            'days_until_conference' => $edition->days_until_conference,
        ];
    }

    /**
     * Build query with edition scope.
     * Helper method for other services to query edition-scoped data.
     * 
     * @param Builder $query
     * @param int|null $year
     * @return Builder
     */
    public function scopeToEdition(Builder $query, ?int $year = null): Builder
    {
        $edition = $this->resolveEdition($year);

        if (!$edition) {
            // Return empty query if edition not found
            return $query->whereRaw('1 = 0');
        }

        return $query->where('edition_id', $edition->id);
    }

    /**
     * Get edition ID from year or active edition.
     * 
     * @param int|null $year
     * @return int|null
     */
    public function resolveEditionId(?int $year = null): ?int
    {
        $edition = $this->resolveEdition($year);

        return $edition ? $edition->id : null;
    }
}
