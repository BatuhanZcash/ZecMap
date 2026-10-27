<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewVerificationRequest;
use App\Models\BusinessVerification;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VerificationRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $requests = VerificationRequest::query()
            ->with(['business:id,name,slug,verified', 'user:id,name,email'])
            ->when(
                in_array($status, [
                    VerificationRequest::STATUS_PENDING,
                    VerificationRequest::STATUS_APPROVED,
                    VerificationRequest::STATUS_REJECTED,
                ], true),
                fn ($query) => $query->where('status', $status),
            )
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = VerificationRequest::query()
            ->where('status', VerificationRequest::STATUS_PENDING)
            ->count();

        $requestsData = $requests->map(fn (VerificationRequest $verificationRequest) => [
            'id' => $verificationRequest->id,
            'status' => $verificationRequest->status,
            'status_label' => $verificationRequest->statusLabel(),
            'outdated_info' => $verificationRequest->outdated_info,
            'how_verified' => $verificationRequest->how_verified,
            'admin_note' => $verificationRequest->admin_note,
            'created_at' => $verificationRequest->created_at?->format('d.m.Y H:i:s'),
            'reviewed_at' => $verificationRequest->reviewed_at?->format('d.m.Y H:i:s'),
            'business' => [
                'id' => $verificationRequest->business?->id,
                'name' => $verificationRequest->business?->name,
                'slug' => $verificationRequest->business?->slug,
                'verified' => (bool) $verificationRequest->business?->verified,
            ],
            'user' => [
                'id' => $verificationRequest->user?->id,
                'name' => $verificationRequest->user?->name,
                'email' => $verificationRequest->user?->email,
            ],
        ])->values();

        return view('Admin.dogrulama', [
            'requests' => $requests,
            'requestsData' => $requestsData,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'totalCount' => $requests->count(),
        ]);
    }

    public function approve(
        ReviewVerificationRequest $request,
        VerificationRequest $verificationRequest,
    ): RedirectResponse {
        if (! $verificationRequest->isPending()) {
            return $this->redirectBack($request, 'Bu istek zaten incelenmiş.');
        }

        DB::transaction(function () use ($request, $verificationRequest) {
            $verificationRequest->update([
                'status' => VerificationRequest::STATUS_APPROVED,
                'admin_note' => $request->input('admin_note'),
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            $verificationRequest->business?->update(['verified' => true]);

            BusinessVerification::query()->firstOrCreate([
                'user_id' => $verificationRequest->user_id,
                'business_id' => $verificationRequest->business_id,
            ]);
        });

        return $this->redirectBack($request, 'Doğrulama isteği onaylandı.');
    }

    public function reject(
        ReviewVerificationRequest $request,
        VerificationRequest $verificationRequest,
    ): RedirectResponse {
        if (! $verificationRequest->isPending()) {
            return $this->redirectBack($request, 'Bu istek zaten incelenmiş.');
        }

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_REJECTED,
            'admin_note' => $request->input('admin_note'),
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return $this->redirectBack($request, 'Doğrulama isteği reddedildi.');
    }

    private function redirectBack(Request $request, string $message): RedirectResponse
    {
        $redirectStatus = $request->input('redirect_status', 'all');

        return redirect()
            ->route('admin.dogrulama', array_filter([
                'status' => $redirectStatus !== 'all' ? $redirectStatus : null,
            ]))
            ->with('success', $message);
    }
}
