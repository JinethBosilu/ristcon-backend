<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Conference;
use App\Models\ContactPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactPersonController extends Controller
{
    /**
     * Get contact persons for a conference year
     */
    public function index(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $contacts = ContactPerson::where('conference_id', $conference->id)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return ApiResponse::success($contacts);
    }

    /**
     * Create a new contact person
     */
    public function store(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $data = $validator->validated();
        $data['conference_id'] = $conference->id;

        $contact = ContactPerson::create($data);

        return ApiResponse::success($contact, 'Contact person created successfully', 201);
    }

    /**
     * Update a contact person
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $contact = ContactPerson::find($id);

        if (!$contact) {
            return ApiResponse::notFound('Contact person not found');
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'string|max:255',
            'role' => 'string|max:255',
            'department' => 'nullable|string|max:255',
            'mobile' => 'string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'email|max:255',
            'address' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $contact->update($validator->validated());

        return ApiResponse::success($contact, 'Contact person updated successfully');
    }

    /**
     * Delete a contact person
     */
    public function destroy(int $id): JsonResponse
    {
        $contact = ContactPerson::find($id);

        if (!$contact) {
            return ApiResponse::notFound('Contact person not found');
        }

        $contact->delete();

        return ApiResponse::success(null, 'Contact person deleted successfully');
    }
}
