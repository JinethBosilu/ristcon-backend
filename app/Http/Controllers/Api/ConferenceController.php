<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceQueryRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ConferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    protected ConferenceService $conferenceService;

    public function __construct(ConferenceService $conferenceService)
    {
        $this->conferenceService = $conferenceService;
    }

    /**
     * Get all conferences
     */
    public function index(ConferenceQueryRequest $request): JsonResponse
    {
        $filters = $request->only(['status', 'year']);
        $includes = $request->input('include');

        $conferences = $this->conferenceService->getAllConferences($filters, $includes);

        return ApiResponse::success(
            $conferences,
            '',
            ['total' => $conferences->count()]
        );
    }

    /**
     * Get conference by year
     */
    public function show(ConferenceQueryRequest $request, int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear(
            $year,
            $request->input('include')
        );

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        return ApiResponse::success($conference);
    }

    /**
     * Get speakers for a conference
     */
    public function speakers(ConferenceQueryRequest $request, int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $speakers = $this->conferenceService->getConferenceSpeakers(
            $conference,
            $request->input('type')
        );

        return ApiResponse::success($speakers);
    }

    /**
     * Get important dates for a conference
     */
    public function importantDates(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $dates = $this->conferenceService->getConferenceDates($conference);

        return ApiResponse::success($dates);
    }

    /**
     * Get committee members for a conference
     */
    public function committees(ConferenceQueryRequest $request, int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $members = $this->conferenceService->getConferenceCommittees(
            $conference,
            $request->input('type')
        );

        // Group by committee type
        $grouped = $members->groupBy('committeeType.committee_name');

        return ApiResponse::success($grouped);
    }

    /**
     * Get contact persons for a conference
     */
    public function contacts(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $contacts = $this->conferenceService->getConferenceContacts($conference);

        return ApiResponse::success($contacts);
    }

    /**
     * Get documents for a conference
     */
    public function documents(ConferenceQueryRequest $request, int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $documents = $this->conferenceService->getConferenceDocuments(
            $conference,
            $request->input('category'),
            $request->has('available') ? filter_var($request->input('available'), FILTER_VALIDATE_BOOLEAN) : null
        );

        return ApiResponse::success($documents);
    }

    /**
     * Get research areas for a conference
     */
    public function researchAreas(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $categories = $this->conferenceService->getConferenceResearchAreas($conference);

        return ApiResponse::success($categories);
    }

    /**
     * Get event location for a conference
     */
    public function location(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $location = $this->conferenceService->getConferenceLocation($conference);

        if (!$location) {
            return ApiResponse::notFound("Location not found for this conference");
        }

        return ApiResponse::success($location);
    }

    /**
     * Get author instructions for a conference
     */
    public function authorInstructions(int $year): JsonResponse
    {
        $data = $this->conferenceService->getAuthorInstructions($year);

        if (empty($data)) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        return ApiResponse::success($data);
    }
}
