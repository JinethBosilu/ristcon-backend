<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Conference;
use App\Models\EventLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventLocationController extends Controller
{
    /**
     * Get event location for a conference
     */
    public function show(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $location = EventLocation::where('conference_id', $conference->id)->first();

        if (!$location) {
            return ApiResponse::notFound("Location not found for this conference");
        }

        return ApiResponse::success($location);
    }

    /**
     * Create or update event location for a conference
     */
    public function upsert(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $validator = Validator::make($request->all(), [
            'venue_name' => 'required|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_maps_embed_url' => 'nullable|string',
            'google_maps_link' => 'nullable|string|max:255',
            'is_virtual' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $location = EventLocation::updateOrCreate(
            ['conference_id' => $conference->id],
            $validator->validated()
        );

        return ApiResponse::success($location, 'Event location saved successfully');
    }

    /**
     * Delete event location for a conference
     */
    public function destroy(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $deleted = EventLocation::where('conference_id', $conference->id)->delete();

        if (!$deleted) {
            return ApiResponse::notFound("Location not found for this conference");
        }

        return ApiResponse::success(null, 'Event location deleted successfully');
    }
}
