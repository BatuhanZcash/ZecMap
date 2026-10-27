<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessReviewRequest;
use App\Models\Business;
use App\Services\AnalyticsRecorder;
use App\Models\BusinessReview;
use App\Models\Community;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class BusinessDetailController extends Controller
{
    public function show(Request $request, Business $business, AnalyticsRecorder $analytics): View
    {
        // Pages exist for active listings and for pending (inactive) submissions awaiting
        // review. Rejected/closed listings stay hidden.
        abort_if($business->status === Business::STATUS_CLOSED, 404);

        $business->increment('view_count');
        $analytics->recordBusinessView($request);
        $business->load([
            'category:id,name,slug',
            'approvedReviews.user:id,name,email,username',
            'approvedReviews.community:id,name,flag_emoji',
        ]);

        $reviews = $business->approvedReviews->sortByDesc('created_at')->values();
        $averageRating = $reviews->avg('rating');
        $userReview = null;
        $hasPendingReview = false;

        if (auth()->check()) {
            $ownReview = BusinessReview::query()
                ->where('business_id', $business->id)
                ->where('user_id', auth()->id())
                ->whereIn('status', [BusinessReview::STATUS_PENDING, BusinessReview::STATUS_APPROVED])
                ->first();

            if ($ownReview?->isApproved()) {
                $userReview = $ownReview;
            } elseif ($ownReview?->isPending()) {
                $hasPendingReview = true;
            }
        }

        $website = $this->normalizeUrl($business->website);
        $socialLinks = is_array($business->social_links) ? $business->social_links : [];
        $twitterUrl = $this->normalizeUrl($socialLinks['twitter'] ?? $socialLinks['x'] ?? null);
        $lastVerification = $business->latestApprovedVerification();
        $hasPendingVerification = auth()->check()
            ? VerificationRequest::query()
                ->where('business_id', $business->id)
                ->where('user_id', auth()->id())
                ->where('status', VerificationRequest::STATUS_PENDING)
                ->exists()
            : false;

        $isPending = ! $business->isActive();
        $canRequestUpdate = $business->payment_provider !== Business::PAYMENT_FLEXA;
        $hasPendingUpdateRequest = auth()->check()
            ? \App\Models\BusinessUpdateRequest::query()
                ->where('business_id', $business->id)
                ->where('user_id', auth()->id())
                ->where('status', \App\Models\BusinessUpdateRequest::STATUS_PENDING)
                ->exists()
            : false;

        return view('business-detail', [
            'business' => $business,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'userReview' => $userReview,
            'hasPendingReview' => $hasPendingReview,
            'coverClass' => $this->coverClassForCategory($business->category?->slug),
            'website' => $website,
            'twitterUrl' => $twitterUrl,
            'lastVerification' => $lastVerification,
            'hasPendingVerification' => $hasPendingVerification,
            'isPending' => $isPending,
            'canRequestUpdate' => $canRequestUpdate,
            'hasPendingUpdateRequest' => $hasPendingUpdateRequest,
            'communities' => Community::query()->orderBy('name')->get(['id', 'name', 'flag_emoji']),
        ]);
    }

    public function poster(Business $business): View
    {
        abort_if($business->status === Business::STATUS_CLOSED, 404);
        abort_if(empty($business->zcash_address), 404);

        return view('business-detail.poster', [
            'business' => $business,
            'zecUri' => \App\Support\Zip321::uri($business->zcash_address),
        ]);
    }

    public function storeReview(StoreBusinessReviewRequest $request, Business $business): RedirectResponse
    {
        abort_unless($business->isActive(), 404);

        if (BusinessReview::blocksNewSubmission($business->id, (int) $request->user()->id)) {
            return redirect()
                ->route('business.show', $business)
                ->with('error', 'You have already reviewed this business or have a review awaiting approval.');
        }

        $communityId = $request->filled('community_id')
            ? (int) $request->input('community_id')
            : null;

        $attributes = [
            'business_id' => $business->id,
            'user_id' => $request->user()->id,
            'rating' => (int) $request->input('rating'),
            'body' => trim((string) $request->input('body')),
            'community_id' => $communityId,
            'status' => BusinessReview::STATUS_PENDING,
        ];

        if ($request->hasFile('image')) {
            $attributes['image_url'] = $this->storeReviewImage(
                $request->file('image'),
                $business,
                (int) $request->user()->id,
            );
        }

        DB::transaction(function () use ($attributes) {
            BusinessReview::query()->create($attributes);
        });

        $successMessage = $communityId !== null
            ? 'Your review has been submitted for approval. After it is published, +5 points will be added to the selected community.'
            : 'Your review has been submitted and is awaiting admin approval.';

        return redirect()
            ->route('business.show', $business)
            ->with('success', $successMessage);
    }

    private function storeReviewImage(UploadedFile $file, Business $business, int $userId): string
    {
        $directory = public_path('assets/images/review-images');
        File::ensureDirectoryExists($directory);

        $filename = ($business->slug ?? 'business').'-'.$userId.'-'.time().'.'.$file->guessExtension();
        $file->move($directory, $filename);

        return 'assets/images/review-images/'.$filename;
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return 'https://'.$url;
    }

    private function coverClassForCategory(?string $slug): string
    {
        return match ($slug) {
            'crypto-wallets' => 'wallets',
            'food-drink' => 'local-merchants',
            'retail' => 'online-shopping',
            'services' => 'vpn-services',
            'travel' => 'local-merchants',
            default => 'default',
        };
    }
}
