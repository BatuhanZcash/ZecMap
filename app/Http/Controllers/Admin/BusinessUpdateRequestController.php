<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewBusinessUpdateRequest;
use App\Models\BusinessUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessUpdateRequestController extends Controller
{
    /**
     * Fields that are applied directly to the business on approval.
     *
     * @var list<string>
     */
    private const SCALAR_FIELDS = ['name', 'description', 'website', 'phone', 'address', 'city'];

    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $requests = BusinessUpdateRequest::query()
            ->with(['business', 'user:id,name,email'])
            ->when(
                in_array($status, [
                    BusinessUpdateRequest::STATUS_PENDING,
                    BusinessUpdateRequest::STATUS_APPROVED,
                    BusinessUpdateRequest::STATUS_REJECTED,
                ], true),
                fn ($query) => $query->where('status', $status),
            )
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = BusinessUpdateRequest::query()
            ->where('status', BusinessUpdateRequest::STATUS_PENDING)
            ->count();

        return view('Admin.guncelleme-istekleri', [
            'requests' => $requests,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'totalCount' => $requests->count(),
            'fieldLabels' => BusinessUpdateRequest::fieldLabels(),
        ]);
    }

    public function approve(
        ReviewBusinessUpdateRequest $request,
        BusinessUpdateRequest $updateRequest,
    ): RedirectResponse {
        if (! $updateRequest->isPending()) {
            return $this->redirectBack($request, 'Bu istek zaten incelenmiş.');
        }

        DB::transaction(function () use ($request, $updateRequest) {
            $business = $updateRequest->business;

            if ($business !== null) {
                $applied = [];
                $changes = is_array($updateRequest->changes) ? $updateRequest->changes : [];

                foreach (self::SCALAR_FIELDS as $field) {
                    if (array_key_exists($field, $changes) && trim((string) $changes[$field]) !== '') {
                        $applied[$field] = $changes[$field];
                    }
                }

                if (! empty($changes['hours'])) {
                    $applied['hours'] = ['raw' => (string) $changes['hours']];
                }

                if (! empty($changes['social_links']) && is_array($changes['social_links'])) {
                    $current = is_array($business->social_links) ? $business->social_links : [];
                    $applied['social_links'] = array_merge($current, $changes['social_links']);
                }

                if ($updateRequest->proposed_logo_url) {
                    $applied['logo_url'] = $updateRequest->proposed_logo_url;
                }

                if ($applied !== []) {
                    $business->update($applied);
                }
            }

            $updateRequest->update([
                'status' => BusinessUpdateRequest::STATUS_APPROVED,
                'admin_note' => $request->input('admin_note'),
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);
        });

        return $this->redirectBack($request, 'Güncelleme isteği onaylandı ve uygulandı.');
    }

    public function reject(
        ReviewBusinessUpdateRequest $request,
        BusinessUpdateRequest $updateRequest,
    ): RedirectResponse {
        if (! $updateRequest->isPending()) {
            return $this->redirectBack($request, 'Bu istek zaten incelenmiş.');
        }

        $updateRequest->update([
            'status' => BusinessUpdateRequest::STATUS_REJECTED,
            'admin_note' => $request->input('admin_note'),
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return $this->redirectBack($request, 'Güncelleme isteği reddedildi.');
    }

    private function redirectBack(Request $request, string $message): RedirectResponse
    {
        $redirectStatus = $request->input('redirect_status', 'pending');

        return redirect()
            ->route('admin.guncelleme-istekleri', array_filter([
                'status' => $redirectStatus !== 'all' ? $redirectStatus : null,
            ]))
            ->with('success', $message);
    }
}
