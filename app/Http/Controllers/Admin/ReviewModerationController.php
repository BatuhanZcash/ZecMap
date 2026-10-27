<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewVerificationRequest;
use App\Models\BusinessReview;
use App\Models\CommunityContribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ReviewModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $reviews = BusinessReview::query()
            ->with([
                'business:id,name,slug',
                'user:id,name,email,username',
                'community:id,name,flag_emoji',
            ])
            ->when(
                in_array($status, [
                    BusinessReview::STATUS_PENDING,
                    BusinessReview::STATUS_APPROVED,
                ], true),
                fn ($query) => $query->where('status', $status),
            )
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = BusinessReview::query()
            ->where('status', BusinessReview::STATUS_PENDING)
            ->count();

        $reviewsData = $reviews->map(fn (BusinessReview $review) => [
            'id' => $review->id,
            'status' => $review->status,
            'status_label' => $review->statusLabel(),
            'rating' => $review->rating,
            'body' => $review->body,
            'image_url' => $review->imageDisplayUrl(),
            'admin_note' => $review->admin_note,
            'created_at' => $review->created_at?->format('d.m.Y H:i:s'),
            'reviewed_at' => $review->reviewed_at?->format('d.m.Y H:i:s'),
            'business' => [
                'id' => $review->business?->id,
                'name' => $review->business?->name,
                'slug' => $review->business?->slug,
            ],
            'user' => [
                'id' => $review->user?->id,
                'name' => $review->user?->name,
                'username' => $review->user?->username,
                'email' => $review->user?->email,
            ],
            'community' => $review->community ? [
                'name' => $review->community->name,
                'flag_emoji' => $review->community->flag_emoji,
            ] : null,
        ])->values();

        return view('Admin.yorumlar', [
            'reviews' => $reviews,
            'reviewsData' => $reviewsData,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'totalCount' => $reviews->count(),
        ]);
    }

    public function approve(
        ReviewVerificationRequest $request,
        BusinessReview $review,
    ): RedirectResponse {
        if (! $review->isPending()) {
            return $this->redirectBack($request, 'Bu yorum zaten incelenmiş.');
        }

        DB::transaction(function () use ($request, $review) {
            $review->update([
                'status' => BusinessReview::STATUS_APPROVED,
                'admin_note' => $request->input('admin_note'),
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            if ($review->community_id !== null) {
                CommunityContribution::query()->firstOrCreate(
                    ['review_id' => $review->id],
                    [
                        'community_id' => $review->community_id,
                        'points' => CommunityContribution::POINTS_REVIEW_COMMUNITY,
                        'contribution_type' => CommunityContribution::TYPE_REVIEW,
                        'user_id' => $review->user_id,
                        'business_id' => $review->business_id,
                    ],
                );
            }
        });

        return $this->redirectBack($request, 'Yorum onaylandı ve yayınlandı.');
    }

    public function reject(
        ReviewVerificationRequest $request,
        BusinessReview $review,
    ): RedirectResponse {
        if (! $review->isPending()) {
            return $this->redirectBack($request, 'Bu yorum zaten incelenmiş.');
        }

        DB::transaction(function () use ($review) {
            CommunityContribution::query()
                ->where('review_id', $review->id)
                ->delete();

            $this->deleteReviewImage($review);
            $review->delete();
        });

        return $this->redirectBack($request, 'Yorum reddedildi.');
    }

    private function deleteReviewImage(BusinessReview $review): void
    {
        if (! $review->image_url) {
            return;
        }

        if (str_starts_with($review->image_url, 'http://') || str_starts_with($review->image_url, 'https://')) {
            return;
        }

        $path = public_path(ltrim($review->image_url, '/'));

        if (is_file($path)) {
            File::delete($path);
        }
    }

    private function redirectBack(Request $request, string $message): RedirectResponse
    {
        $redirectStatus = $request->input('redirect_status', 'pending');

        return redirect()
            ->route('admin.yorumlar', array_filter([
                'status' => $redirectStatus !== 'all' ? $redirectStatus : null,
            ]))
            ->with('success', $message);
    }
}
