<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    /**
     * Get all conferences
     */
    public function index(Request $request): JsonResponse
    {
        $query = Conference::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Include relations
        if ($request->has('include')) {
            $includes = explode(',', $request->include);
            
            $relationMap = [
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
                'social_media' => 'socialMediaLinks',
                'abstract_formats' => 'abstractFormats',
            ];

            foreach ($includes as $include) {
                $include = trim($include);
                if (isset($relationMap[$include])) {
                    $query->with($relationMap[$include]);
                }
            }
        }

        $conferences = $query->orderBy('year', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $conferences,
            'meta' => [
                'total' => $conferences->count()
            ]
        ]);
    }

    /**
     * Get conference by year
     */
    public function show(Request $request, int $year): JsonResponse
    {
        $query = Conference::where('year', $year);

        // Include relations
        if ($request->has('include')) {
            $includes = explode(',', $request->include);
            
            $relationMap = [
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

            foreach ($includes as $include) {
                $include = trim($include);
                if (isset($relationMap[$include])) {
                    $query->with($relationMap[$include]);
                }
            }
        }

        $conference = $query->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $conference
        ]);
    }

    /**
     * Get speakers for a conference
     */
    public function speakers(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $query = $conference->speakers();

        // Filter by speaker type
        if ($request->has('type')) {
            $query->where('speaker_type', $request->type);
        }

        $speakers = $query->get();

        return response()->json([
            'success' => true,
            'data' => $speakers
        ]);
    }

    /**
     * Get important dates for a conference
     */
    public function importantDates(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $dates = $conference->importantDates;

        return response()->json([
            'success' => true,
            'data' => $dates
        ]);
    }

    /**
     * Get committee members for a conference
     */
    public function committees(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $query = $conference->committeeMembers()->with('committeeType');

        // Filter by committee type
        if ($request->has('type')) {
            $query->whereHas('committeeType', function ($q) use ($request) {
                $q->where('committee_name', $request->type);
            });
        }

        $members = $query->get()->groupBy('committeeType.committee_name');

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    /**
     * Get contact persons for a conference
     */
    public function contacts(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $contacts = $conference->contactPersons;

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    /**
     * Get documents for a conference
     */
    public function documents(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $query = $conference->documents();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by availability
        if ($request->has('available')) {
            $available = filter_var($request->available, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_available', $available);
        }

        $documents = $query->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Get research areas for a conference
     */
    public function researchAreas(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $categories = $conference->researchCategories()
            ->with('researchAreas')
            ->active()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get event location for a conference
     */
    public function location(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        $location = $conference->eventLocation;

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => "Location not found for this conference"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }

    /**
     * Get author instructions for a conference
     */
    public function authorInstructions(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)
            ->with([
                'authorPageConfig',
                'submissionMethods',
                'presentationGuidelines'
            ])
            ->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => "Conference not found for year {$year}"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'config' => $conference->authorPageConfig,
                'submission_methods' => $conference->submissionMethods,
                'presentation_guidelines' => $conference->presentationGuidelines,
            ]
        ]);
    }
}
