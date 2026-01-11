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

    // ==================== EDITION MANAGEMENT ====================

    /**
     * Create a new conference edition
     */
    public function storeEdition(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer|unique:conference_editions,year',
            'edition_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'theme' => 'required|string|max:500',
            'description' => 'nullable|string',
            'conference_date' => 'required|date',
            'venue_type' => 'required|string|in:physical,virtual,hybrid',
            'venue_location' => 'nullable|string|max:500',
            'general_email' => 'required|email',
            'copyright_year' => 'required|integer|min:2020|max:2100',
        ]);

        // Generate slug from year
        $slug = (string) $request->year;

        $edition = \App\Models\ConferenceEdition::create([
            'year' => $request->year,
            'edition_number' => $request->edition_number,
            'name' => $request->name,
            'slug' => $slug,
            'theme' => $request->theme,
            'description' => $request->description,
            'conference_date' => $request->conference_date,
            'venue_type' => $request->venue_type,
            'venue_location' => $request->venue_location,
            'general_email' => $request->general_email,
            'copyright_year' => $request->copyright_year,
            'status' => 'draft',
            'is_active_edition' => false,
            'site_version' => '1.0',
        ]);

        return ApiResponse::created($edition, 'Conference edition created successfully');
    }

    /**
     * Update a conference edition
     */
    public function updateEdition(Request $request, int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $request->validate([
            'year' => 'sometimes|integer|unique:conference_editions,year,' . $id,
            'edition_number' => 'sometimes|integer|min:1',
            'name' => 'sometimes|string|max:255',
            'theme' => 'sometimes|string|max:500',
            'description' => 'nullable|string',
            'conference_date' => 'sometimes|date',
            'venue_type' => 'sometimes|string|in:physical,virtual,hybrid',
            'venue_location' => 'nullable|string|max:500',
            'general_email' => 'sometimes|email',
            'copyright_year' => 'sometimes|integer|min:2020|max:2100',
        ]);

        // Update slug if year changes
        $data = $request->all();
        if (isset($data['year'])) {
            $data['slug'] = (string) $data['year'];
        }

        $edition->update($data);

        return ApiResponse::success($edition, 'Conference edition updated successfully');
    }

    /**
     * Delete a conference edition
     */
    public function deleteEdition(int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        if (!$edition->canBeDeleted()) {
            return ApiResponse::error(
                'Cannot delete this edition. Active and published editions cannot be deleted.',
                400
            );
        }

        $edition->delete();

        return ApiResponse::success(null, 'Conference edition deleted successfully');
    }

    /**
     * Mark edition as active
     */
    public function activateEdition(int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $edition->markAsActive();

        return ApiResponse::success($edition, 'Edition marked as active successfully');
    }

    /**
     * Publish an edition
     */
    public function publishEdition(int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $edition->publish();

        return ApiResponse::success($edition, 'Edition published successfully');
    }

    /**
     * Archive an edition
     */
    public function archiveEdition(int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        if (!$edition->archive()) {
            return ApiResponse::error('Cannot archive active edition', 400);
        }

        return ApiResponse::success($edition, 'Edition archived successfully');
    }

    // ==================== SPEAKERS MANAGEMENT ====================

    /**
     * Get all speakers for an edition
     */
    public function getEditionSpeakers(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $speakers = $edition->speakers()
            ->orderBy('display_order')
            ->orderBy('full_name')
            ->get();

        return ApiResponse::success($speakers);
    }

    /**
     * Create a new speaker for an edition
     */
    public function createSpeaker(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $request->validate([
            'speaker_type' => 'required|string|in:keynote,plenary,invited',
            'full_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'affiliation' => 'required|string|max:255',
            'additional_affiliation' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $speaker = $edition->speakers()->create([
            'conference_id' => $edition->year,
            'edition_id' => $edition->id,
            'speaker_type' => $request->speaker_type,
            'full_name' => $request->full_name,
            'title' => $request->title,
            'affiliation' => $request->affiliation,
            'additional_affiliation' => $request->additional_affiliation,
            'bio' => $request->bio,
            'email' => $request->email,
            'website_url' => $request->website_url,
            'display_order' => $request->display_order ?? 0,
        ]);

        return ApiResponse::created($speaker, 'Speaker created successfully');
    }

    /**
     * Update a speaker
     */
    public function updateSpeaker(Request $request, int $id): JsonResponse
    {
        $speaker = \App\Models\Speaker::find($id);

        if (!$speaker) {
            return ApiResponse::notFound("Speaker not found");
        }

        $request->validate([
            'speaker_type' => 'sometimes|string|in:keynote,plenary,invited',
            'full_name' => 'sometimes|string|max:255',
            'title' => 'nullable|string|max:255',
            'affiliation' => 'sometimes|string|max:255',
            'additional_affiliation' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $speaker->update($request->all());

        return ApiResponse::success($speaker, 'Speaker updated successfully');
    }

    /**
     * Delete a speaker
     */
    public function deleteSpeaker(int $id): JsonResponse
    {
        $speaker = \App\Models\Speaker::find($id);

        if (!$speaker) {
            return ApiResponse::notFound("Speaker not found");
        }

        // Delete photo if exists
        if ($speaker->photo_filename) {
            \Storage::disk('public')->delete('speakers/' . $speaker->photo_filename);
        }

        $speaker->delete();

        return ApiResponse::success(null, 'Speaker deleted successfully');
    }

    /**
     * Upload speaker photo
     */
    public function uploadSpeakerPhoto(Request $request, int $id): JsonResponse
    {
        $speaker = \App\Models\Speaker::find($id);

        if (!$speaker) {
            return ApiResponse::notFound("Speaker not found");
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120', // Max 5MB
        ]);

        // Delete old photo if exists
        if ($speaker->photo_filename) {
            \Storage::disk('public')->delete('speakers/' . $speaker->photo_filename);
        }

        // Store new photo
        $file = $request->file('photo');
        $filename = time() . '_' . $speaker->id . '.' . $file->getClientOriginalExtension();
        $file->storeAs('speakers', $filename, 'public');

        $speaker->update(['photo_filename' => $filename]);

        return ApiResponse::success($speaker, 'Speaker photo uploaded successfully');
    }

    /**
     * Delete speaker photo
     */
    public function deleteSpeakerPhoto(int $id): JsonResponse
    {
        $speaker = \App\Models\Speaker::find($id);

        if (!$speaker) {
            return ApiResponse::notFound("Speaker not found");
        }

        if (!$speaker->photo_filename) {
            return ApiResponse::error('Speaker has no photo', 400);
        }

        // Delete photo file
        \Storage::disk('public')->delete('speakers/' . $speaker->photo_filename);

        $speaker->update(['photo_filename' => null]);

        return ApiResponse::success($speaker, 'Speaker photo deleted successfully');
    }
}

