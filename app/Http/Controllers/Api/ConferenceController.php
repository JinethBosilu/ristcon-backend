<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceQueryRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ConferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * Create a new conference edition by cloning from active edition
     */
    public function storeEdition(Request $request): JsonResponse
    {
        $request->validate([
            'year' => [
                'required',
                'integer',
                Rule::unique('conference_editions', 'year')->whereNull('deleted_at')
            ],
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

        try {
            // Wrap in transaction to ensure all-or-nothing operation
            $edition = \DB::transaction(function () use ($request) {
                // Generate slug from year
                $slug = (string) $request->year;

                // Create the new edition
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

                // Find the currently active edition to clone from
                $activeEdition = \App\Models\ConferenceEdition::where('is_active_edition', true)->first();

                if ($activeEdition) {
                    // Clone all related data from active edition
                    $this->cloneEditionData($activeEdition, $edition);
                } else {
                    // If no active edition exists, create default data
                    $this->createDefaultData($edition, $request->conference_date);
                }

                return $edition;
            });

            $activeEdition = \App\Models\ConferenceEdition::where('is_active_edition', true)->first();
            $message = $activeEdition 
                ? 'Conference edition created successfully by cloning from ' . $activeEdition->name
                : 'Conference edition created successfully with default data';

            return ApiResponse::success($edition, $message, [], 201);

        } catch (\Exception $e) {
            \Log::error('Failed to create conference edition: ' . $e->getMessage(), [
                'year' => $request->year,
                'exception' => $e,
            ]);
            
            return ApiResponse::error(
                'Failed to create conference edition: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Clone all related data from source edition to target edition
     */
    private function cloneEditionData($sourceEdition, $targetEdition): void
    {
        $newEditionId = $targetEdition->id;  // Primary key is 'id', not 'edition_id'
        $newConferenceDate = $targetEdition->conference_date;
        
        // Get legacy conference_id (for backward compatibility with old schema)
        $conferenceId = \App\Models\Conference::first()->id ?? 1;

        // Clone important dates (set all to new conference_date)
        foreach ($sourceEdition->importantDates as $date) {
            \App\Models\ImportantDate::create([
                'conference_id' => $conferenceId,  // Legacy column for backward compatibility
                'edition_id' => $newEditionId,
                'date_type' => $date->date_type,
                'display_label' => $date->display_label,  // Fixed: was date_label
                'date_value' => $newConferenceDate,
                'notes' => $date->notes,
                'display_order' => $date->display_order,
            ]);
        }

        // Clone speakers
        foreach ($sourceEdition->speakers as $speaker) {
            \App\Models\Speaker::create([
                'conference_id' => $conferenceId,  // Legacy column for backward compatibility
                'edition_id' => $newEditionId,
                'full_name' => $speaker->full_name,
                'title' => $speaker->title,
                'affiliation' => $speaker->affiliation,
                'additional_affiliation' => $speaker->additional_affiliation,
                'bio' => $speaker->bio,
                'photo_filename' => $speaker->photo_filename,
                'website_url' => $speaker->website_url,
                'email' => $speaker->email,
                'speaker_type' => $speaker->speaker_type,
                'display_order' => $speaker->display_order,
            ]);
        }

        // Clone committees
        foreach ($sourceEdition->committeeMembers as $committee) {
            \App\Models\CommitteeMember::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'committee_type_id' => $committee->committee_type_id,
                'full_name' => $committee->full_name,
                'designation' => $committee->designation,
                'department' => $committee->department,
                'role' => $committee->role,
                'role_category' => $committee->role_category,
                'affiliation' => $committee->affiliation,
                'country' => $committee->country,
                'is_international' => $committee->is_international,
                'display_order' => $committee->display_order,
            ]);
        }

        // Clone research categories
        foreach ($sourceEdition->researchCategories as $category) {
            $newCategory = \App\Models\ResearchCategory::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'category_name' => $category->category_name,
                'category_code' => $category->category_code,
                'description' => $category->description,
                'display_order' => $category->display_order,
            ]);

            // Clone research areas for this category
            foreach ($category->researchAreas as $area) {
                \App\Models\ResearchArea::create([
                    'conference_id' => $conferenceId,
                    'edition_id' => $newEditionId,
                    'category_id' => $newCategory->id,
                    'area_name' => $area->area_name,
                    'description' => $area->description,
                    'display_order' => $area->display_order,
                ]);
            }
        }

        // Clone contacts
        foreach ($sourceEdition->contactPersons as $contact) {
            \App\Models\ContactPerson::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'full_name' => $contact->full_name,
                'role' => $contact->role,
                'department' => $contact->department,
                'mobile' => $contact->mobile,
                'phone' => $contact->phone,
                'email' => $contact->email,
                'address' => $contact->address,
                'display_order' => $contact->display_order,
            ]);
        }

        // Clone social media links
        foreach ($sourceEdition->socialMediaLinks as $link) {
            \App\Models\SocialMediaLink::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'platform' => $link->platform,
                'url' => $link->url,
                'display_order' => $link->display_order,
            ]);
        }

        // Clone location
        if ($sourceEdition->eventLocation) {
            \App\Models\EventLocation::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'venue_name' => $sourceEdition->eventLocation->venue_name,
                'full_address' => $sourceEdition->eventLocation->full_address,
                'city' => $sourceEdition->eventLocation->city,
                'country' => $sourceEdition->eventLocation->country,
                'latitude' => $sourceEdition->eventLocation->latitude,
                'longitude' => $sourceEdition->eventLocation->longitude,
                'google_maps_embed_url' => $sourceEdition->eventLocation->google_maps_embed_url,
                'google_maps_link' => $sourceEdition->eventLocation->google_maps_link,
                'is_virtual' => $sourceEdition->eventLocation->is_virtual,
            ]);
        }

        // Clone author instructions
        if ($sourceEdition->authorPageConfig) {
            \App\Models\AuthorPageConfig::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'conference_format' => $sourceEdition->authorPageConfig->conference_format,
                'cmt_url' => $sourceEdition->authorPageConfig->cmt_url,
                'submission_email' => $sourceEdition->authorPageConfig->submission_email,
                'blind_review_enabled' => $sourceEdition->authorPageConfig->blind_review_enabled,
                'camera_ready_required' => $sourceEdition->authorPageConfig->camera_ready_required,
                'special_instructions' => $sourceEdition->authorPageConfig->special_instructions,
                'acknowledgment_text' => $sourceEdition->authorPageConfig->acknowledgment_text,
            ]);
        }

        // Clone submission methods
        foreach ($sourceEdition->submissionMethods as $method) {
            \App\Models\SubmissionMethod::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'document_type' => $method->document_type,
                'submission_method' => $method->submission_method,
                'email_address' => $method->email_address,
                'notes' => $method->notes,
                'display_order' => $method->display_order,
            ]);
        }

        // Clone presentation guidelines
        foreach ($sourceEdition->presentationGuidelines as $guideline) {
            \App\Models\PresentationGuideline::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'presentation_type' => $guideline->presentation_type,
                'duration_minutes' => $guideline->duration_minutes,
                'presentation_minutes' => $guideline->presentation_minutes,
                'qa_minutes' => $guideline->qa_minutes,
                'poster_width' => $guideline->poster_width,
                'poster_height' => $guideline->poster_height,
                'poster_unit' => $guideline->poster_unit,
                'poster_orientation' => $guideline->poster_orientation,
                'physical_presence_required' => $guideline->physical_presence_required,
                'detailed_requirements' => $guideline->detailed_requirements,
                'display_order' => $guideline->display_order,
            ]);
        }

        // Clone payment policies
        foreach ($sourceEdition->paymentPolicies as $policy) {
            \App\Models\PaymentPolicy::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'policy_type' => $policy->policy_type,
                'policy_text' => $policy->policy_text,
                'display_order' => $policy->display_order,
            ]);
        }

        // Clone registration fees
        foreach ($sourceEdition->registrationFees as $fee) {
            \App\Models\RegistrationFee::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'attendee_type' => $fee->attendee_type,
                'currency' => $fee->currency,
                'amount' => $fee->amount,
                'early_bird_amount' => $fee->early_bird_amount,
                'early_bird_deadline' => $fee->early_bird_deadline,
                'is_active' => $fee->is_active,
                'display_order' => $fee->display_order,
            ]);
        }

        // Clone payment information
        foreach ($sourceEdition->paymentInformation as $payment) {
            \App\Models\PaymentInformation::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'payment_type' => $payment->payment_type,
                'bank_name' => $payment->bank_name,
                'account_name' => $payment->account_name,
                'account_number' => $payment->account_number,
                'swift_code' => $payment->swift_code,
                'iban' => $payment->iban,
                'branch_name' => $payment->branch_name,
                'branch_code' => $payment->branch_code,
                'additional_info' => $payment->additional_info,
                'display_order' => $payment->display_order,
            ]);
        }

        // Clone abstract formats
        foreach ($sourceEdition->abstractFormats as $format) {
            \App\Models\AbstractFormat::create([
                'conference_id' => $conferenceId,
                'edition_id' => $newEditionId,
                'format_type' => $format->format_type,
                'max_title_characters' => $format->max_title_characters,
                'title_font_name' => $format->title_font_name,
                'title_font_size' => $format->title_font_size,
                'title_style' => $format->title_style,
                'max_body_words' => $format->max_body_words,
                'body_font_name' => $format->body_font_name,
                'body_font_size' => $format->body_font_size,
                'body_line_spacing' => $format->body_line_spacing,
                'max_keywords' => $format->max_keywords,
                'keywords_font_name' => $format->keywords_font_name,
                'keywords_font_size' => $format->keywords_font_size,
                'keywords_style' => $format->keywords_style,
                'max_references' => $format->max_references,
                'sections' => $format->sections,
                'additional_notes' => $format->additional_notes,
                'display_order' => $format->display_order,
            ]);
        }

        // Note: We intentionally DO NOT clone documents and assets 
        // because they require actual file uploads. Admins must upload files manually for each edition.
    }

    /**
     * Create default data when no active edition exists to clone from
     */
    private function createDefaultData($edition, $conferenceDate): void
    {
        // Get legacy conference_id
        $conferenceId = \App\Models\Conference::first()->id ?? 1;
        
        $defaultDates = [
            ['date_type' => 'submission_deadline', 'display_label' => 'Abstract Submission Deadline', 'display_order' => 1],
            ['date_type' => 'notification', 'display_label' => 'Notification of Acceptance', 'display_order' => 2],
            ['date_type' => 'camera_ready', 'display_label' => 'Camera-Ready Submission', 'display_order' => 3],
            ['date_type' => 'conference_date', 'display_label' => 'Conference Date', 'display_order' => 4],
            ['date_type' => 'registration_deadline', 'display_label' => 'Registration Deadline', 'display_order' => 5],
            ['date_type' => 'other', 'display_label' => 'Early Bird Deadline', 'display_order' => 6],
            ['date_type' => 'other', 'display_label' => 'Late Registration', 'display_order' => 7],
        ];

        foreach ($defaultDates as $dateData) {
            \App\Models\ImportantDate::create([
                'conference_id' => $conferenceId,
                'edition_id' => $edition->id,  // Primary key is 'id', not 'edition_id'
                'date_type' => $dateData['date_type'],
                'display_label' => $dateData['display_label'],
                'date_value' => $conferenceDate,
                'display_order' => $dateData['display_order'],
            ]);
        }
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
                'Cannot delete this edition. Published editions must be set as draft or archived first. Active editions cannot be deleted.',
                400
            );
        }

        try {
            // Perform hard delete (cascade will remove all related records)
            $edition->forceDelete();
            
            return ApiResponse::success(null, 'Conference edition deleted permanently');
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete conference edition: ' . $e->getMessage(), [
                'edition_id' => $id,
                'exception' => $e,
            ]);
            
            return ApiResponse::error(
                'Failed to delete conference edition: ' . $e->getMessage(),
                500
            );
        }
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

    /**
     * Set an edition as draft (unpublish)
     */
    public function draftEdition(int $id): JsonResponse
    {
        $edition = \App\Models\ConferenceEdition::find($id);

        if (!$edition) {
            return ApiResponse::notFound("Conference edition not found");
        }

        if (!$edition->draft()) {
            return ApiResponse::error('Cannot set active edition as draft', 400);
        }

        return ApiResponse::success($edition, 'Edition set as draft successfully');
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
            'conference_id' => 1, // Legacy field - always 1 for RISTCON
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

        return ApiResponse::success($speaker, 'Speaker created successfully', [], 201);
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
        \Log::info('Upload speaker photo request received', [
            'speaker_id' => $id,
            'has_file' => $request->hasFile('photo'),
            'file_valid' => $request->hasFile('photo') ? $request->file('photo')->isValid() : false,
            'all_data' => $request->all(),
        ]);

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
            'display_label' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $date = $edition->importantDates()->create([
            'conference_id' => 1, // Legacy field - always 1 for RISTCON
            'edition_id' => $edition->id,
            'date_type' => $request->date_type,
            'date_value' => $request->date_value,
            'is_extended' => $request->is_extended ?? false,
            'display_label' => $request->display_label,
            'notes' => $request->notes,
            'display_order' => $request->display_order ?? 0,
        ]);

        return ApiResponse::success($date, 'Important date created successfully', [], 201);
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
            'display_label' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $date->update($request->only([
            'date_type',
            'date_value',
            'is_extended',
            'display_label',
            'notes',
            'display_order',
        ]));

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
            'conference_id' => 1, // Legacy field - always 1 for RISTCON
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

        return ApiResponse::success($document, 'Document created successfully', [], 201);
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
            'display_order' => 'integer|min:0',
        ]);

        $validated['edition_id'] = $editionId;
        $validated['conference_id'] = 1; // Legacy field - always 1 for RISTCON

        $member = \App\Models\CommitteeMember::create($validated);
        $member->load('committeeType');

        return ApiResponse::success($member, 'Committee member created successfully', [], 201);
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
        $validated['conference_id'] = 1; // Legacy field - always 1 for RISTCON

        $category = \App\Models\ResearchCategory::create($validated);

        return ApiResponse::success($category, 'Research category created successfully', [], 201);
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

        return ApiResponse::success($area, 'Research area created successfully', [], 201);
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
            'conference_id' => 1, // Legacy field - always 1 for RISTCON
            'asset_type' => $validated['asset_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'alt_text' => $validated['alt_text'] ?? null,
            'usage_context' => $validated['usage_context'] ?? null,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return ApiResponse::success($asset, 'Asset created successfully', [], 201);
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

    /**
     * Delete asset file (keep metadata)
     */
    public function deleteAssetFile(int $id): JsonResponse
    {
        $asset = \App\Models\ConferenceAsset::find($id);

        if (!$asset) {
            return ApiResponse::notFound("Asset not found");
        }

        // Delete file from storage
        if ($asset->file_path) {
            \Storage::disk('public')->delete($asset->file_path);
        }

        // Clear file-related fields but keep the asset record
        $asset->update([
            'file_path' => '',
            'mime_type' => '',
            'file_size' => 0,
        ]);

        return ApiResponse::success($asset, 'Asset file deleted successfully');
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
        $validated['conference_id'] = 1; // Legacy field - always 1 for RISTCON
        $contact = \App\Models\ContactPerson::create($validated);

        return ApiResponse::success($contact, 'Contact created successfully', [], 201);
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
        $validated['conference_id'] = 1; // Legacy field - always 1 for RISTCON
        $validated['label'] = $validated['label'] ?? ''; // Default to empty string if not provided
        $socialMedia = \App\Models\SocialMediaLink::create($validated);

        return ApiResponse::success($socialMedia, 'Social media link created successfully', [], 201);
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
        $validated['conference_id'] = 1; // Legacy field - always 1 for RISTCON
        $paymentInfo = \App\Models\PaymentInformation::create($validated);

        return ApiResponse::success($paymentInfo, 'Payment information created successfully', [], 201);
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

    /**
     * Get past conference editions
     * Returns all editions prior to the specified year (or current year if not provided)
     * with appropriate website URLs
     * 
     * @param int|null $year The year to use as reference (optional, defaults to current year)
     */
    public function getPastEvents(?int $year = null): JsonResponse
    {
        $referenceYear = $year ?? now()->year;
        
        $pastEditions = \App\Models\ConferenceEdition::where('year', '<', $referenceYear)
            ->where('status', '!=', 'cancelled')
            ->orderBy('year', 'desc')
            ->get()
            ->map(function ($edition) {
                return [
                    'id' => $edition->id,
                    'year' => $edition->year,
                    'edition_number' => $edition->edition_number,
                    'name' => $edition->name,
                    'theme' => $edition->theme,
                    'conference_date' => $edition->conference_date,
                    'venue_type' => $edition->venue_type,
                    'venue_location' => $edition->venue_location,
                    'website_type' => $edition->is_legacy_site ? 'legacy' : 'unified',
                    'url' => $edition->is_legacy_site 
                        ? $edition->legacy_website_url 
                        : "/ristcon/{$edition->year}",
                    'is_legacy' => $edition->is_legacy_site,
                ];
            });

        return ApiResponse::success(
            $pastEditions,
            'Past conference editions retrieved successfully',
            ['total' => $pastEditions->count(), 'reference_year' => $referenceYear]
        );
    }
}


