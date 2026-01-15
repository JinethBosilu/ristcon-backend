<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrationFeeRequest;
use App\Http\Requests\UpdateRegistrationFeeRequest;
use App\Http\Responses\ApiResponse;
use App\Models\RegistrationFee;
use App\Models\PaymentPolicy;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Get registration information including fees and payment policies.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $fees = RegistrationFee::whereHas('edition', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        $policies = PaymentPolicy::whereHas('edition', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return ApiResponse::success([
            'fees' => $fees,
            'policies' => $policies,
        ]);
    }

    /**
     * Get registration fees only.
     */
    public function fees(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $fees = RegistrationFee::whereHas('edition', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return ApiResponse::success($fees);
    }

    /**
     * Get payment policies only.
     */
    public function policies(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $policies = PaymentPolicy::whereHas('edition', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return ApiResponse::success($policies);
    }

    /**
     * Store a newly created registration fee.
     */
    public function store(StoreRegistrationFeeRequest $request)
    {
        $validated = $request->validated();

        $fee = RegistrationFee::create($validated);

        return ApiResponse::success($fee, 'Registration fee created successfully', [], 201);
    }

    /**
     * Update the specified registration fee.
     */
    public function update(UpdateRegistrationFeeRequest $request, string $id)
    {
        $fee = RegistrationFee::findOrFail($id);

        $validated = $request->validated();

        $fee->update($validated);

        return ApiResponse::success($fee, 'Registration fee updated successfully');
    }

    /**
     * Remove the specified registration fee.
     */
    public function destroy(string $id)
    {
        $fee = RegistrationFee::findOrFail($id);
        $fee->delete();

        return ApiResponse::success(null, 'Registration fee deleted successfully');
    }
}
