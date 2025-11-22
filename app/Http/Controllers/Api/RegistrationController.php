<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        
        $fees = RegistrationFee::whereHas('conference', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        $policies = PaymentPolicy::whereHas('conference', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'fees' => $fees,
                'policies' => $policies,
            ],
        ]);
    }

    /**
     * Get registration fees only.
     */
    public function fees(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $fees = RegistrationFee::whereHas('conference', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $fees,
        ]);
    }

    /**
     * Get payment policies only.
     */
    public function policies(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $policies = PaymentPolicy::whereHas('conference', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $policies,
        ]);
    }

    /**
     * Store a newly created registration fee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'attendee_type' => 'required|string',
            'currency' => 'required|string',
            'amount' => 'required|numeric',
            'early_bird_amount' => 'nullable|numeric',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'nullable|integer',
        ]);

        $fee = RegistrationFee::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $fee,
        ], 201);
    }

    /**
     * Update the specified registration fee.
     */
    public function update(Request $request, string $id)
    {
        $fee = RegistrationFee::findOrFail($id);

        $validated = $request->validate([
            'attendee_type' => 'sometimes|string',
            'currency' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'early_bird_amount' => 'nullable|numeric',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'nullable|integer',
        ]);

        $fee->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $fee,
        ]);
    }

    /**
     * Remove the specified registration fee.
     */
    public function destroy(string $id)
    {
        $fee = RegistrationFee::findOrFail($id);
        $fee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Registration fee deleted successfully',
        ]);
    }
}
