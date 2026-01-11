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

    // ==================== IMPORTANT DATES MANAGEMENT ====================

    /**
     * Get all important dates for an edition
     */
    public function getEditionDates(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $dates = $edition->importantDates()
            ->orderBy('display_order')
            ->orderBy('date_value')
            ->get();

        return ApiResponse::success($dates);
    }

    /**
     * Create a new important date for an edition
     */
    public function createDate(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $request->validate([
            'date_type' => 'required|string|in:submission_deadline,notification,camera_ready,conference_date,registration_deadline,other',
            'date_value' => 'required|date',
            'is_extended' => 'nullable|boolean',
            'original_date' => 'nullable|date',
            'display_label' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $date = $edition->importantDates()->create([
            'conference_id' => $edition->year,
            'edition_id' => $edition->id,
            'date_type' => $request->date_type,
            'date_value' => $request->date_value,
            'is_extended' => $request->is_extended ?? false,
            'original_date' => $request->original_date,
            'display_label' => $request->display_label,
            'notes' => $request->notes,
            'display_order' => $request->display_order ?? 0,
        ]);

        return ApiResponse::created($date, 'Important date created successfully');
    }

    /**
     * Update an important date
     */
    public function updateDate(Request $request, int $id): JsonResponse
    {
        $date = \App\Models\ImportantDate::find($id);

        if (!$date) {
            return ApiResponse::notFound("Important date not found");
        }

        $request->validate([
            'date_type' => 'sometimes|string|in:submission_deadline,notification,camera_ready,conference_date,registration_deadline,other',
            'date_value' => 'sometimes|date',
            'is_extended' => 'nullable|boolean',
            'original_date' => 'nullable|date',
            'display_label' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $date->update($request->all());

        return ApiResponse::success($date, 'Important date updated successfully');
    }

    /**
     * Delete an important date
     */
    public function deleteDate(int $id): JsonResponse
    {
        $date = \App\Models\ImportantDate::find($id);

        if (!$date) {
            return ApiResponse::notFound("Important date not found");
        }

        $date->delete();

        return ApiResponse::success(null, 'Important date deleted successfully');
    }

    // ==================== DOCUMENTS MANAGEMENT ====================

    /**
     * Get all documents for an edition
     */
    public function getEditionDocuments(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $documents = $edition->documents()
            ->orderBy('display_order')
            ->orderBy('display_name')
            ->get();

        return ApiResponse::success($documents);
    }

    /**
     * Create a new document for an edition
     */
    public function createDocument(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        $request->validate([
            'document_category' => 'required|string|in:abstract_template,author_form,registration_form,presentation_template,camera_ready_template,flyer,other',
            'display_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'button_width_percent' => 'nullable|integer|min:1|max:100',
            'display_order' => 'nullable|integer|min:0',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:10240', // Max 10MB
        ]);

        // Handle file upload
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = $edition->documents()->create([
            'conference_id' => $edition->year,
            'edition_id' => $edition->id,
            'document_category' => $request->document_category,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'display_name' => $request->display_name,
            'is_active' => $request->is_active ?? true,
            'button_width_percent' => $request->button_width_percent ?? 100,
            'display_order' => $request->display_order ?? 0,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return ApiResponse::created($document, 'Document created successfully');
    }

    /**
     * Update a document
     */
    public function updateDocument(Request $request, int $id): JsonResponse
    {
        $document = \App\Models\ConferenceDocument::find($id);

        if (!$document) {
            return ApiResponse::notFound("Document not found");
        }

        $request->validate([
            'document_category' => 'sometimes|string|in:abstract_template,author_form,registration_form,presentation_template,camera_ready_template,flyer,other',
            'display_name' => 'sometimes|string|max:255',
            'is_active' => 'nullable|boolean',
            'button_width_percent' => 'nullable|integer|min:1|max:100',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $document->update($request->all());

        return ApiResponse::success($document, 'Document updated successfully');
    }

    /**
     * Delete a document
     */
    public function deleteDocument(int $id): JsonResponse
    {
        $document = \App\Models\ConferenceDocument::find($id);

        if (!$document) {
            return ApiResponse::notFound("Document not found");
        }

        // Delete file if exists
        if ($document->file_path) {
            \Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return ApiResponse::success(null, 'Document deleted successfully');
    }

    /**
     * Upload/replace document file
     */
    public function uploadDocumentFile(Request $request, int $id): JsonResponse
    {
        $document = \App\Models\ConferenceDocument::find($id);

        if (!$document) {
            return ApiResponse::notFound("Document not found");
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:10240', // Max 10MB
        ]);

        // Delete old file if exists
        if ($document->file_path) {
            \Storage::disk('public')->delete($document->file_path);
        }

        // Store new file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document->update([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return ApiResponse::success($document, 'Document file uploaded successfully');
    }

    /**
     * Get all committee types
     */
    public function getCommitteeTypes(): JsonResponse
    {
        $types = \App\Models\CommitteeType::orderBy('display_order')->get();
        return ApiResponse::success($types);
    }

    /**
     * Get committee members for an edition
     */
    public function getEditionCommitteeMembers(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $members = \App\Models\CommitteeMember::where('edition_id', $editionId)
            ->with('committeeType')
            ->orderBy('display_order')
            ->get();

        return ApiResponse::success($members);
    }

    /**
     * Create a new committee member
     */
    public function createCommitteeMember(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $validated = $request->validate([
            'committee_type_id' => 'required|exists:committee_types,id',
            'full_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'affiliation' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'role_category' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_international' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $validated['edition_id'] = $editionId;
        $validated['conference_id'] = $edition->conference_id;

        $member = \App\Models\CommitteeMember::create($validated);
        $member->load('committeeType');

        return ApiResponse::success($member, 'Committee member created successfully', 201);
    }

    /**
     * Update committee member
     */
    public function updateCommitteeMember(Request $request, int $id): JsonResponse
    {
        $member = \App\Models\CommitteeMember::find($id);

        if (!$member) {
            return ApiResponse::notFound("Committee member not found");
        }

        $validated = $request->validate([
            'committee_type_id' => 'sometimes|required|exists:committee_types,id',
            'full_name' => 'sometimes|required|string|max:255',
            'designation' => 'sometimes|required|string|max:255',
            'department' => 'nullable|string|max:255',
            'affiliation' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|max:255',
            'role_category' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_international' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $member->update($validated);
        $member->load('committeeType');

        return ApiResponse::success($member, 'Committee member updated successfully');
    }

    /**
     * Delete committee member
     */
    public function deleteCommitteeMember(int $id): JsonResponse
    {
        $member = \App\Models\CommitteeMember::find($id);

        if (!$member) {
            return ApiResponse::notFound("Committee member not found");
        }

        // Delete photo if exists
        if ($member->photo_path) {
            \Storage::disk('public')->delete($member->photo_path);
        }

        $member->delete();

        return ApiResponse::success(null, 'Committee member deleted successfully');
    }

    /**
     * Upload committee member photo
     */
    public function uploadCommitteeMemberPhoto(Request $request, int $id): JsonResponse
    {
        $member = \App\Models\CommitteeMember::find($id);

        if (!$member) {
            return ApiResponse::notFound("Committee member not found");
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120', // Max 5MB
        ]);

        // Delete old photo if exists
        if ($member->photo_path) {
            \Storage::disk('public')->delete($member->photo_path);
        }

        // Store new photo
        $photo = $request->file('photo');
        $fileName = time() . '_' . $photo->getClientOriginalName();
        $photoPath = $photo->storeAs('committee', $fileName, 'public');

        $member->update([
            'photo_path' => $photoPath,
        ]);

        $member->load('committeeType');

        return ApiResponse::success($member, 'Photo uploaded successfully');
    }

    /**
     * Delete committee member photo
     */
    public function deleteCommitteeMemberPhoto(int $id): JsonResponse
    {
        $member = \App\Models\CommitteeMember::find($id);

        if (!$member) {
            return ApiResponse::notFound("Committee member not found");
        }

        if (!$member->photo_path) {
            return ApiResponse::error('No photo to delete', 400);
        }

        \Storage::disk('public')->delete($member->photo_path);

        $member->update([
            'photo_path' => null,
        ]);

        $member->load('committeeType');

        return ApiResponse::success($member, 'Photo deleted successfully');
    }

    /**
     * Get research categories for an edition
     */
    public function getEditionResearchCategories(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $categories = \App\Models\ResearchCategory::where('edition_id', $editionId)
            ->withCount('researchAreas')
            ->orderBy('display_order')
            ->get();

        return ApiResponse::success($categories);
    }

    /**
     * Create a new research category
     */
    public function createResearchCategory(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $validated = $request->validate([
            'category_code' => 'required|string|max:10',
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $validated['edition_id'] = $editionId;
        $validated['conference_id'] = $edition->conference_id;

        $category = \App\Models\ResearchCategory::create($validated);

        return ApiResponse::success($category, 'Research category created successfully', 201);
    }

    /**
     * Update research category
     */
    public function updateResearchCategory(Request $request, int $id): JsonResponse
    {
        $category = \App\Models\ResearchCategory::find($id);

        if (!$category) {
            return ApiResponse::notFound("Research category not found");
        }

        $validated = $request->validate([
            'category_code' => 'sometimes|required|string|max:10',
            'category_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $category->update($validated);

        return ApiResponse::success($category, 'Research category updated successfully');
    }

    /**
     * Delete research category
     */
    public function deleteResearchCategory(int $id): JsonResponse
    {
        $category = \App\Models\ResearchCategory::find($id);

        if (!$category) {
            return ApiResponse::notFound("Research category not found");
        }

        $category->delete();

        return ApiResponse::success(null, 'Research category deleted successfully');
    }

    /**
     * Get research areas for a category
     */
    public function getCategoryResearchAreas(int $categoryId): JsonResponse
    {
        $category = \App\Models\ResearchCategory::find($categoryId);

        if (!$category) {
            return ApiResponse::notFound("Research category not found");
        }

        $areas = \App\Models\ResearchArea::where('category_id', $categoryId)
            ->orderBy('display_order')
            ->get();

        return ApiResponse::success($areas);
    }

    /**
     * Create a new research area
     */
    public function createResearchArea(Request $request, int $categoryId): JsonResponse
    {
        $category = \App\Models\ResearchCategory::find($categoryId);

        if (!$category) {
            return ApiResponse::notFound("Research category not found");
        }

        $validated = $request->validate([
            'area_name' => 'required|string|max:255',
            'alternate_names' => 'nullable|array',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $validated['category_id'] = $categoryId;

        $area = \App\Models\ResearchArea::create($validated);

        return ApiResponse::success($area, 'Research area created successfully', 201);
    }

    /**
     * Update research area
     */
    public function updateResearchArea(Request $request, int $id): JsonResponse
    {
        $area = \App\Models\ResearchArea::find($id);

        if (!$area) {
            return ApiResponse::notFound("Research area not found");
        }

        $validated = $request->validate([
            'area_name' => 'sometimes|required|string|max:255',
            'alternate_names' => 'nullable|array',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $area->update($validated);

        return ApiResponse::success($area, 'Research area updated successfully');
    }

    /**
     * Delete research area
     */
    public function deleteResearchArea(int $id): JsonResponse
    {
        $area = \App\Models\ResearchArea::find($id);

        if (!$area) {
            return ApiResponse::notFound("Research area not found");
        }

        $area->delete();

        return ApiResponse::success(null, 'Research area deleted successfully');
    }

    /**
     * Get assets for an edition
     */
    public function getEditionAssets(int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $assets = \App\Models\ConferenceAsset::where('edition_id', $editionId)
            ->orderBy('asset_type')
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success($assets);
    }

    /**
     * Create a new asset
     */
    public function createAsset(Request $request, int $editionId): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($editionId);

        if (!$edition) {
            return ApiResponse::notFound("Edition not found");
        }

        $validated = $request->validate([
            'asset_type' => 'required|in:logo,poster,banner,brochure,image,other',
            'file' => 'required|image|mimes:jpeg,jpg,png,svg,webp|max:5120', // Max 5MB
            'alt_text' => 'nullable|string|max:255',
            'usage_context' => 'nullable|string|max:255',
        ]);

        // Store file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('assets', $fileName, 'public');

        $asset = \App\Models\ConferenceAsset::create([
            'edition_id' => $editionId,
            'conference_id' => $edition->conference_id,
            'asset_type' => $validated['asset_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'alt_text' => $validated['alt_text'] ?? null,
            'usage_context' => $validated['usage_context'] ?? null,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return ApiResponse::success($asset, 'Asset created successfully', 201);
    }

    /**
     * Update asset metadata
     */
    public function updateAsset(Request $request, int $id): JsonResponse
    {
        $asset = \App\Models\ConferenceAsset::find($id);

        if (!$asset) {
            return ApiResponse::notFound("Asset not found");
        }

        $validated = $request->validate([
            'asset_type' => 'sometimes|required|in:logo,poster,banner,brochure,image,other',
            'alt_text' => 'nullable|string|max:255',
            'usage_context' => 'nullable|string|max:255',
        ]);

        $asset->update($validated);

        return ApiResponse::success($asset, 'Asset updated successfully');
    }

    /**
     * Delete asset
     */
    public function deleteAsset(int $id): JsonResponse
    {
        $asset = \App\Models\ConferenceAsset::find($id);

        if (!$asset) {
            return ApiResponse::notFound("Asset not found");
        }

        // Delete file from storage
        if ($asset->file_path) {
            \Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return ApiResponse::success(null, 'Asset deleted successfully');
    }

    /**
     * Upload/replace asset file
     */
    public function uploadAssetFile(Request $request, int $id): JsonResponse
    {
        $asset = \App\Models\ConferenceAsset::find($id);

        if (!$asset) {
            return ApiResponse::notFound("Asset not found");
        }

        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png,svg,webp|max:5120', // Max 5MB
        ]);

        // Delete old file if exists
        if ($asset->file_path) {
            \Storage::disk('public')->delete($asset->file_path);
        }

        // Store new file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('assets', $fileName, 'public');

        $asset->update([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return ApiResponse::success($asset, 'Asset file uploaded successfully');
    }

    // ==================== Event Locations Management ====================
    
    public function getEditionLocations(int $editionId): JsonResponse
    {
        $locations = \App\Models\EventLocation::where('edition_id', $editionId)
            ->orderBy('id')
            ->get();
        return ApiResponse::success($locations);
    }

    public function createLocation(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'venue_name' => 'required|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_maps_embed_url' => 'nullable|url',
            'google_maps_link' => 'nullable|url',
            'is_virtual' => 'boolean',
        ]);

        $validated['edition_id'] = $editionId;
        $location = \App\Models\EventLocation::create($validated);

        return ApiResponse::success($location, 'Location created successfully', 201);
    }

    public function updateLocation(Request $request, int $id): JsonResponse
    {
        $location = \App\Models\EventLocation::find($id);
        if (!$location) {
            return ApiResponse::notFound('Location not found');
        }

        $validated = $request->validate([
            'venue_name' => 'string|max:255',
            'full_address' => 'string',
            'city' => 'string|max:255',
            'country' => 'string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_maps_embed_url' => 'nullable|url',
            'google_maps_link' => 'nullable|url',
            'is_virtual' => 'boolean',
        ]);

        $location->update($validated);
        return ApiResponse::success($location, 'Location updated successfully');
    }

    public function deleteLocation(int $id): JsonResponse
    {
        $location = \App\Models\EventLocation::find($id);
        if (!$location) {
            return ApiResponse::notFound('Location not found');
        }

        $location->delete();
        return ApiResponse::success(null, 'Location deleted successfully');
    }

    // ==================== Contact Persons Management ====================
    
    public function getEditionContacts(int $editionId): JsonResponse
    {
        $contacts = \App\Models\ContactPerson::where('edition_id', $editionId)
            ->orderBy('display_order')
            ->get();
        return ApiResponse::success($contacts);
    }

    public function createContact(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
        ]);

        $validated['edition_id'] = $editionId;
        $contact = \App\Models\ContactPerson::create($validated);

        return ApiResponse::success($contact, 'Contact created successfully', 201);
    }

    public function updateContact(Request $request, int $id): JsonResponse
    {
        $contact = \App\Models\ContactPerson::find($id);
        if (!$contact) {
            return ApiResponse::notFound('Contact not found');
        }

        $validated = $request->validate([
            'full_name' => 'string|max:255',
            'role' => 'string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'email|max:255',
            'address' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        $contact->update($validated);
        return ApiResponse::success($contact, 'Contact updated successfully');
    }

    public function deleteContact(int $id): JsonResponse
    {
        $contact = \App\Models\ContactPerson::find($id);
        if (!$contact) {
            return ApiResponse::notFound('Contact not found');
        }

        $contact->delete();
        return ApiResponse::success(null, 'Contact deleted successfully');
    }

    // ==================== Social Media Links Management ====================
    
    public function getEditionSocialMedia(int $editionId): JsonResponse
    {
        $socialMedia = \App\Models\SocialMediaLink::where('edition_id', $editionId)
            ->orderBy('display_order')
            ->get();
        return ApiResponse::success($socialMedia);
    }

    public function createSocialMedia(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|in:facebook,twitter,linkedin,instagram,youtube,email',
            'url' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['edition_id'] = $editionId;
        $socialMedia = \App\Models\SocialMediaLink::create($validated);

        return ApiResponse::success($socialMedia, 'Social media link created successfully', 201);
    }

    public function updateSocialMedia(Request $request, int $id): JsonResponse
    {
        $socialMedia = \App\Models\SocialMediaLink::find($id);
        if (!$socialMedia) {
            return ApiResponse::notFound('Social media link not found');
        }

        $validated = $request->validate([
            'platform' => 'in:facebook,twitter,linkedin,instagram,youtube,email',
            'url' => 'string|max:255',
            'label' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $socialMedia->update($validated);
        return ApiResponse::success($socialMedia, 'Social media link updated successfully');
    }

    public function deleteSocialMedia(int $id): JsonResponse
    {
        $socialMedia = \App\Models\SocialMediaLink::find($id);
        if (!$socialMedia) {
            return ApiResponse::notFound('Social media link not found');
        }

        $socialMedia->delete();
        return ApiResponse::success(null, 'Social media link deleted successfully');
    }

    // ==================== Registration Fees Management ====================
    
    public function getEditionRegistrationFees(int $editionId): JsonResponse
    {
        $fees = \App\Models\RegistrationFee::where('edition_id', $editionId)
            ->orderBy('display_order')
            ->get();
        return ApiResponse::success($fees);
    }

    public function createRegistrationFee(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'attendee_type' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['edition_id'] = $editionId;
        $fee = \App\Models\RegistrationFee::create($validated);

        return ApiResponse::success($fee, 'Registration fee created successfully', 201);
    }

    public function updateRegistrationFee(Request $request, int $id): JsonResponse
    {
        $fee = \App\Models\RegistrationFee::find($id);
        if (!$fee) {
            return ApiResponse::notFound('Registration fee not found');
        }

        $validated = $request->validate([
            'attendee_type' => 'string|max:100',
            'currency' => 'string|max:10',
            'amount' => 'numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $fee->update($validated);
        return ApiResponse::success($fee, 'Registration fee updated successfully');
    }

    public function deleteRegistrationFee(int $id): JsonResponse
    {
        $fee = \App\Models\RegistrationFee::find($id);
        if (!$fee) {
            return ApiResponse::notFound('Registration fee not found');
        }

        $fee->delete();
        return ApiResponse::success(null, 'Registration fee deleted successfully');
    }

    // ==================== Payment Information Management ====================
    
    public function getEditionPaymentInfo(int $editionId): JsonResponse
    {
        $paymentInfo = \App\Models\PaymentInformation::where('edition_id', $editionId)
            ->orderBy('display_order')
            ->get();
        return ApiResponse::success($paymentInfo);
    }

    public function createPaymentInfo(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:local,foreign',
            'beneficiary_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'swift_code' => 'nullable|string|max:20',
            'branch_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'additional_info' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['edition_id'] = $editionId;
        $paymentInfo = \App\Models\PaymentInformation::create($validated);

        return ApiResponse::success($paymentInfo, 'Payment information created successfully', 201);
    }

    public function updatePaymentInfo(Request $request, int $id): JsonResponse
    {
        $paymentInfo = \App\Models\PaymentInformation::find($id);
        if (!$paymentInfo) {
            return ApiResponse::notFound('Payment information not found');
        }

        $validated = $request->validate([
            'payment_type' => 'in:local,foreign',
            'beneficiary_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'swift_code' => 'nullable|string|max:20',
            'branch_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'additional_info' => 'nullable|string',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $paymentInfo->update($validated);
        return ApiResponse::success($paymentInfo, 'Payment information updated successfully');
    }

    public function deletePaymentInfo(int $id): JsonResponse
    {
        $paymentInfo = \App\Models\PaymentInformation::find($id);
        if (!$paymentInfo) {
            return ApiResponse::notFound('Payment information not found');
        }

        $paymentInfo->delete();
        return ApiResponse::success(null, 'Payment information deleted successfully');
    }

    // ==================== Submission Methods Management ====================
    
    public function getEditionSubmissionMethods(int $editionId): JsonResponse
    {
        $methods = \App\Models\SubmissionMethod::where('edition_id', $editionId)
            ->orderBy('display_order')
            ->get();
        return ApiResponse::success($methods);
    }

    public function createSubmissionMethod(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|in:author_info,abstract,extended_abstract,camera_ready,other',
            'submission_method' => 'required|in:email,cmt_upload,online_form,postal',
            'email_address' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
        ]);

        $validated['edition_id'] = $editionId;
        $method = \App\Models\SubmissionMethod::create($validated);

        return ApiResponse::success($method, 'Submission method created successfully', 201);
    }

    public function updateSubmissionMethod(Request $request, int $id): JsonResponse
    {
        $method = \App\Models\SubmissionMethod::find($id);
        if (!$method) {
            return ApiResponse::notFound('Submission method not found');
        }

        $validated = $request->validate([
            'document_type' => 'in:author_info,abstract,extended_abstract,camera_ready,other',
            'submission_method' => 'in:email,cmt_upload,online_form,postal',
            'email_address' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        $method->update($validated);
        return ApiResponse::success($method, 'Submission method updated successfully');
    }

    public function deleteSubmissionMethod(int $id): JsonResponse
    {
        $method = \App\Models\SubmissionMethod::find($id);
        if (!$method) {
            return ApiResponse::notFound('Submission method not found');
        }

        $method->delete();
        return ApiResponse::success(null, 'Submission method deleted successfully');
    }

    // ==================== Presentation Guidelines Management ====================
    
    public function getEditionPresentationGuidelines(int $editionId): JsonResponse
    {
        $guidelines = \App\Models\PresentationGuideline::where('edition_id', $editionId)
            ->orderBy('presentation_type')
            ->get();
        return ApiResponse::success($guidelines);
    }

    public function createPresentationGuideline(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'presentation_type' => 'required|in:oral,poster,workshop,panel',
            'duration_minutes' => 'nullable|integer|min:0',
            'presentation_minutes' => 'nullable|integer|min:0',
            'qa_minutes' => 'nullable|integer|min:0',
            'poster_width' => 'nullable|numeric|min:0',
            'poster_height' => 'nullable|numeric|min:0',
            'poster_unit' => 'nullable|in:inches,cm,mm',
            'poster_orientation' => 'nullable|in:portrait,landscape',
            'physical_presence_required' => 'boolean',
            'detailed_requirements' => 'nullable|string',
        ]);

        $validated['edition_id'] = $editionId;
        $guideline = \App\Models\PresentationGuideline::create($validated);

        return ApiResponse::success($guideline, 'Presentation guideline created successfully', 201);
    }

    public function updatePresentationGuideline(Request $request, int $id): JsonResponse
    {
        $guideline = \App\Models\PresentationGuideline::find($id);
        if (!$guideline) {
            return ApiResponse::notFound('Presentation guideline not found');
        }

        $validated = $request->validate([
            'presentation_type' => 'in:oral,poster,workshop,panel',
            'duration_minutes' => 'nullable|integer|min:0',
            'presentation_minutes' => 'nullable|integer|min:0',
            'qa_minutes' => 'nullable|integer|min:0',
            'poster_width' => 'nullable|numeric|min:0',
            'poster_height' => 'nullable|numeric|min:0',
            'poster_unit' => 'nullable|in:inches,cm,mm',
            'poster_orientation' => 'nullable|in:portrait,landscape',
            'physical_presence_required' => 'boolean',
            'detailed_requirements' => 'nullable|string',
        ]);

        $guideline->update($validated);
        return ApiResponse::success($guideline, 'Presentation guideline updated successfully');
    }

    public function deletePresentationGuideline(int $id): JsonResponse
    {
        $guideline = \App\Models\PresentationGuideline::find($id);
        if (!$guideline) {
            return ApiResponse::notFound('Presentation guideline not found');
        }

        $guideline->delete();
        return ApiResponse::success(null, 'Presentation guideline deleted successfully');
    }

    // ==================== Author Page Config Management ====================
    
    public function getEditionAuthorConfig(int $editionId): JsonResponse
    {
        $config = \App\Models\AuthorPageConfig::where('edition_id', $editionId)->first();
        return ApiResponse::success($config);
    }

    public function createAuthorConfig(Request $request, int $editionId): JsonResponse
    {
        $validated = $request->validate([
            'conference_format' => 'required|in:in_person,virtual,hybrid',
            'cmt_url' => 'nullable|url|max:255',
            'submission_email' => 'nullable|email|max:255',
            'blind_review_enabled' => 'boolean',
            'camera_ready_required' => 'boolean',
            'special_instructions' => 'nullable|string',
            'acknowledgment_text' => 'nullable|string',
        ]);

        $validated['edition_id'] = $editionId;
        $config = \App\Models\AuthorPageConfig::create($validated);

        return ApiResponse::success($config, 'Author config created successfully', 201);
    }

    public function updateAuthorConfig(Request $request, int $id): JsonResponse
    {
        $config = \App\Models\AuthorPageConfig::find($id);
        if (!$config) {
            return ApiResponse::notFound('Author config not found');
        }

        $validated = $request->validate([
            'conference_format' => 'in:in_person,virtual,hybrid',
            'cmt_url' => 'nullable|url|max:255',
            'submission_email' => 'nullable|email|max:255',
            'blind_review_enabled' => 'boolean',
            'camera_ready_required' => 'boolean',
            'special_instructions' => 'nullable|string',
            'acknowledgment_text' => 'nullable|string',
        ]);

        $config->update($validated);
        return ApiResponse::success($config, 'Author config updated successfully');
    }

    public function deleteAuthorConfig(int $id): JsonResponse
    {
        $config = \App\Models\AuthorPageConfig::find($id);
        if (!$config) {
            return ApiResponse::notFound('Author config not found');
        }

        $config->delete();
        return ApiResponse::success(null, 'Author config deleted successfully');
    }
}


