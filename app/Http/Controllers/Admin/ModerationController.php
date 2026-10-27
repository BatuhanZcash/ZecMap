<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $applications = Business::query()
            ->with(['owner:id,name,email', 'category:id,name'])
            ->when($status === 'pending', fn ($query) => $query->where('status', Business::STATUS_INACTIVE))
            ->when($status === 'approved', fn ($query) => $query->where('status', Business::STATUS_ACTIVE))
            ->when($status === 'rejected', fn ($query) => $query->where('status', Business::STATUS_CLOSED))
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = Business::query()
            ->where('status', Business::STATUS_INACTIVE)
            ->count();

        $applicationsData = $applications->map(fn (Business $business) => [
            'id' => $business->id,
            'name' => $business->name,
            'slug' => $business->slug,
            'address' => $business->fullAddress(),
            'email' => $business->contactEmail(),
            'website' => $business->website,
            'phone' => $business->phone,
            'telegram' => $business->social_links['telegram'] ?? null,
            'description' => $business->description,
            'logo_url' => $business->logoDisplayUrl(),
            'zcash_address' => $business->zcash_address,
            'payment_provider' => $business->payment_provider,
            'category' => $business->category?->name,
            'owner_name' => $business->owner?->name,
            'status' => $business->moderationStatus(),
            'status_label' => $business->moderationStatusLabel(),
            'created_at' => $business->created_at?->format('d.m.Y H:i'),
            'is_pending' => $business->status === Business::STATUS_INACTIVE,
        ])->values();

        return view('Admin.moderasyon', [
            'applications' => $applications,
            'applicationsData' => $applicationsData,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'totalCount' => $applications->count(),
        ]);
    }

    public function approve(Request $request, Business $business): RedirectResponse
    {
        if ($business->status !== Business::STATUS_INACTIVE) {
            return $this->redirectBack($request, 'Bu başvuru zaten incelenmiş.');
        }

        $business->update(['status' => Business::STATUS_ACTIVE]);

        return $this->redirectBack($request, 'Başvuru onaylandı.');
    }

    public function reject(Request $request, Business $business): RedirectResponse
    {
        if ($business->status !== Business::STATUS_INACTIVE) {
            return $this->redirectBack($request, 'Bu başvuru zaten incelenmiş.');
        }

        $business->update(['status' => Business::STATUS_CLOSED]);

        return $this->redirectBack($request, 'Başvuru reddedildi.');
    }

    private function redirectBack(Request $request, string $message): RedirectResponse
    {
        $redirectStatus = $request->input('redirect_status', 'pending');

        return redirect()
            ->route('admin.moderasyon', array_filter([
                'status' => $redirectStatus !== 'all' ? $redirectStatus : null,
            ]))
            ->with('success', $message);
    }
}
