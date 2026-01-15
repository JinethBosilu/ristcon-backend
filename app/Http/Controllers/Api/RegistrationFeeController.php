<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Conference;
use App\Models\RegistrationFee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationFeeController extends Controller
{
    /**
     * Get registration fees for a conference year
     */
    public function index(int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $fees = RegistrationFee::where('conference_id', $conference->id)
            ->orderBy('display_order')
            ->orderBy('fee_id')
            ->get();

        return ApiResponse::success($fees);
    }

    /**
     * Create a new registration fee
     */
    public function store(Request $request, int $year): JsonResponse
    {
        $conference = Conference::where('year', $year)->first();

        if (!$conference) {
            return ApiResponse::notFound("Conference not found for year {$year}");
        }

        $validator = Validator::make($request->all(), [
            'attendee_type' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $data = $validator->validated();
        $data['conference_id'] = $conference->id;

        $fee = RegistrationFee::create($data);

        return ApiResponse::success($fee, 'Registration fee created successfully', 201);
    }

    /**
     * Update a registration fee
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fee = RegistrationFee::find($id);

        if (!$fee) {
            return ApiResponse::notFound('Registration fee not found');
        }

        $validator = Validator::make($request->all(), [
            'attendee_type' => 'string|max:100',
            'currency' => 'string|max:10',
            'amount' => 'numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $fee->update($validator->validated());

        return ApiResponse::success($fee, 'Registration fee updated successfully');
    }

    /**
     * Delete a registration fee
     */
    public function destroy(int $id): JsonResponse
    {
        $fee = RegistrationFee::find($id);

        if (!$fee) {
            return ApiResponse::notFound('Registration fee not found');
        }

        $fee->delete();

        return ApiResponse::success(null, 'Registration fee deleted successfully');
    }
}
