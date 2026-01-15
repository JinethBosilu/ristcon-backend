<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentInformationRequest;
use App\Http\Requests\UpdatePaymentInformationRequest;
use App\Http\Responses\ApiResponse;
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
        
        $paymentInfo = PaymentInformation::whereHas('edition', function ($query) use ($year) {
            $query->where('year', $year);
        })
        ->orderBy('display_order')
        ->get();

        return ApiResponse::success($paymentInfo);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentInformationRequest $request)
    {
        $validated = $request->validated();

        $paymentInfo = PaymentInformation::create($validated);

        return ApiResponse::success($paymentInfo, 'Payment information created successfully', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);

        return ApiResponse::success($paymentInfo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentInformationRequest $request, string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);

        $validated = $request->validated();

        $paymentInfo->update($validated);

        return ApiResponse::success($paymentInfo, 'Payment information updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentInfo = PaymentInformation::findOrFail($id);
        $paymentInfo->delete();

        return ApiResponse::success(null, 'Payment information deleted successfully');
    }
}
