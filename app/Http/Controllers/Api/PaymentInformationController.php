<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentInformation;
use Illuminate\Http\Request;

class PaymentInformationController extends Controller
{
    /**
     * Display payment information for a specific conference.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $paymentInfo = PaymentInformation::whereHas('conference', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $paymentInfo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'payment_type' => 'required|in:local,foreign',
            'beneficiary_name' => 'required|string',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'swift_code' => 'nullable|string',
            'branch_code' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'currency' => 'required|string',
            'additional_info' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        $paymentInfo = PaymentInformation::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $paymentInfo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $paymentInfo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);

        $validated = $request->validate([
            'payment_type' => 'sometimes|in:local,foreign',
            'beneficiary_name' => 'sometimes|string',
            'bank_name' => 'sometimes|string',
            'account_number' => 'sometimes|string',
            'swift_code' => 'nullable|string',
            'branch_code' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'currency' => 'sometimes|string',
            'additional_info' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        $paymentInfo->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $paymentInfo,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);
        $paymentInfo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment information deleted successfully',
        ]);
    }
}
