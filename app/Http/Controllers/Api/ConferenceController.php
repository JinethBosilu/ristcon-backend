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

    /**
     * Get assets for a conference
     */
    public function assets(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $assets = $conference->assets;

        return ApiResponse::success($assets);
    }

    /**
     * Get social media links for a conference
     */
    public function socialMediaLinks(int $year): JsonResponse
    {
        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $socialMedia = $conference->socialMediaLinks()->orderBy('display_order')->get();

        return ApiResponse::success($socialMedia);
    }

    /**
     * Add a social media link
     */
    public function addSocialMediaLink(Request $request, int $year): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,twitter,linkedin,instagram,youtube,email,other',
            'url' => 'required|string',
            'label' => 'required|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $socialMedia = $conference->socialMediaLinks()->create($request->all());

        return ApiResponse::created($socialMedia, 'Social media link added successfully');
    }

    /**
     * Update a social media link
     */
    public function updateSocialMediaLink(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'platform' => 'sometimes|string|in:facebook,twitter,linkedin,instagram,youtube,email,other',
            'url' => 'sometimes|string',
            'label' => 'sometimes|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $socialMedia = \App\Models\SocialMediaLink::find($id);

        if (!$socialMedia) {
            return ApiResponse::notFound("Social media link not found");
        }

        $socialMedia->update($request->all());

        return ApiResponse::success($socialMedia, 'Social media link updated successfully');
    }

    /**
     * Delete a social media link
     */
    public function deleteSocialMediaLink(int $id): JsonResponse
    {
        $socialMedia = \App\Models\SocialMediaLink::find($id);

        if (!$socialMedia) {
            return ApiResponse::notFound("Social media link not found");
        }

        $socialMedia->delete();

        return ApiResponse::success(null, 'Social media link deleted successfully');
    }

    /**
     * Update author page configuration
     */
    public function updateAuthorConfig(Request $request, int $year): JsonResponse
    {
        $request->validate([
            'conference_format' => 'sometimes|string|in:in_person,virtual,hybrid',
            'cmt_url' => 'nullable|string',
            'submission_email' => 'nullable|email',
            'blind_review_enabled' => 'nullable|boolean',
            'camera_ready_required' => 'nullable|boolean',
            'special_instructions' => 'nullable|string',
            'acknowledgment_text' => 'nullable|string',
        ]);

        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $config = $conference->authorPageConfig;

        if (!$config) {
            // Create if doesn't exist
            $config = $conference->authorPageConfig()->create($request->all());
        } else {
            $config->update($request->all());
        }

        return ApiResponse::success($config, 'Author configuration updated successfully');
    }

    /**
     * Add submission method
     */
    public function addSubmissionMethod(Request $request, int $year): JsonResponse
    {
        $request->validate([
            'document_type' => 'required|string|in:author_info,abstract,extended_abstract,full_paper,poster',
            'submission_method' => 'required|string|in:email,cmt_upload,online_form,other',
            'email_address' => 'nullable|email',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $method = $conference->submissionMethods()->create($request->all());

        return ApiResponse::created($method, 'Submission method added successfully');
    }

    /**
     * Update submission method
     */
    public function updateSubmissionMethod(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'document_type' => 'sometimes|string|in:author_info,abstract,extended_abstract,full_paper,poster',
            'submission_method' => 'sometimes|string|in:email,cmt_upload,online_form,other',
            'email_address' => 'nullable|email',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        $method = \App\Models\SubmissionMethod::find($id);

        if (!$method) {
            return ApiResponse::notFound("Submission method not found");
        }

        $method->update($request->all());

        return ApiResponse::success($method, 'Submission method updated successfully');
    }

    /**
     * Delete submission method
     */
    public function deleteSubmissionMethod(int $id): JsonResponse
    {
        $method = \App\Models\SubmissionMethod::find($id);

        if (!$method) {
            return ApiResponse::notFound("Submission method not found");
        }

        $method->delete();

        return ApiResponse::success(null, 'Submission method deleted successfully');
    }

    /**
     * Add presentation guideline
     */
    public function addPresentationGuideline(Request $request, int $year): JsonResponse
    {
        $request->validate([
            'presentation_type' => 'required|string|in:oral,poster,workshop',
            'duration_minutes' => 'nullable|integer',
            'presentation_minutes' => 'nullable|integer',
            'qa_minutes' => 'nullable|integer',
            'poster_width' => 'nullable|numeric',
            'poster_height' => 'nullable|numeric',
            'poster_unit' => 'nullable|string|in:inches,cm',
            'poster_orientation' => 'nullable|string|in:portrait,landscape',
            'physical_presence_required' => 'nullable|boolean',
            'detailed_requirements' => 'nullable|string',
        ]);

        $conference = $this->conferenceService->getConferenceByYear($year);

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $guideline = $conference->presentationGuidelines()->create($request->all());

        return ApiResponse::created($guideline, 'Presentation guideline added successfully');
    }

    /**
     * Update presentation guideline
     */
    public function updatePresentationGuideline(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'presentation_type' => 'sometimes|string|in:oral,poster,workshop',
            'duration_minutes' => 'nullable|integer',
            'presentation_minutes' => 'nullable|integer',
            'qa_minutes' => 'nullable|integer',
            'poster_width' => 'nullable|numeric',
            'poster_height' => 'nullable|numeric',
            'poster_unit' => 'nullable|string|in:inches,cm',
            'poster_orientation' => 'nullable|string|in:portrait,landscape',
            'physical_presence_required' => 'nullable|boolean',
            'detailed_requirements' => 'nullable|string',
        ]);

        $guideline = \App\Models\PresentationGuideline::find($id);

        if (!$guideline) {
            return ApiResponse::notFound("Presentation guideline not found");
        }

        $guideline->update($request->all());

        return ApiResponse::success($guideline, 'Presentation guideline updated successfully');
    }

    /**
     * Delete presentation guideline
     */
    public function deletePresentationGuideline(int $id): JsonResponse
    {
        $guideline = \App\Models\PresentationGuideline::find($id);

        if (!$guideline) {
            return ApiResponse::notFound("Presentation guideline not found");
        }

        $guideline->delete();

        return ApiResponse::success(null, 'Presentation guideline deleted successfully');
    }
}

