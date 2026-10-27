<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVerificationRequest;
use App\Models\Business;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerificationRequestController extends Controller
{
    public function store(StoreVerificationRequest $request, Business $business): RedirectResponse
    {
        $validated = $request->validated();
        $userId = (int) auth()->id();

        $existingPending = VerificationRequest::query()
            ->where('business_id', $business->id)
            ->where('user_id', $userId)
            ->where('status', VerificationRequest::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return redirect()
                ->route('business.show', $business)
                ->with('error', 'You already have a pending verification request for this business.');
        }

        VerificationRequest::query()->create([
            'business_id' => $business->id,
            'user_id' => $userId,
            'outdated_info' => $validated['outdated_info'] ?? null,
            'how_verified' => trim($validated['how_verified']),
            'status' => VerificationRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('business.show', $business)
            ->with('success', 'Verification request submitted. Our team will review it soon.');
    }
}
