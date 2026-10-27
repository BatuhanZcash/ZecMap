<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusinessReviewRequest;
use App\Http\Requests\StoreVerificationRequest;
use App\Http\Resources\BusinessDetailResource;
use App\Http\Resources\BusinessReviewResource;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\Community;
use App\Models\VerificationRequest;
use App\Services\AnalyticsRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BusinessController extends Controller
{
    public function show(Request $request, string $slug, AnalyticsRecorder $analytics): JsonResponse
    {
        $business = Business::query()
            ->with([
                'category:id,name,slug',
                'approvedReviews.user:id,name,username,email',
                'approvedReviews.community:id,name,slug,flag_emoji',
            ])
            ->where('slug', $slug)
            ->where('status', Business::STATUS_ACTIVE)
            ->firstOrFail();

        $business->increment('view_count');
        $analytics->recordBusinessView($request);

        $reviews = $business->approvedReviews->sortByDesc('created_at')->values();
        $averageRating = $reviews->isEmpty() ? null : (float) $reviews->avg('rating');
        $user = Auth::guard('sanctum')->user();

        $userReview = null;
        $hasPendingVerification = false;
        $hasPendingReview = false;

        if ($user) {
            $ownReview = BusinessReview::query()
                ->where('business_id', $business->id)
                ->where('user_id', $user->id)
                ->whereIn('status', [BusinessReview::STATUS_PENDING, BusinessReview::STATUS_APPROVED])
                ->first();

            if ($ownReview?->isApproved()) {
                $userReview = (new BusinessReviewResource($ownReview))->resolve();
            } elseif ($ownReview?->isPending()) {
                $hasPendingReview = true;
            }

            $hasPendingVerification = VerificationRequest::query()
                ->where('business_id', $business->id)
                ->where('user_id', $user->id)
                ->where('status', VerificationRequest::STATUS_PENDING)
                ->exists();
        }

        $lastVerification = $business->latestApprovedVerification();
        $socialLinks = is_array($business->social_links) ? $business->social_links : [];

        $resource = new BusinessDetailResource(
            $business,
            $reviews,
            $averageRating,
            $userReview,
            $hasPendingReview,
            $hasPendingVerification,
            $lastVerification ? [
                'user_name' => $lastVerification->user?->name ?? 'Community member',
                'reviewed_at' => $lastVerification->reviewed_at?->toIso8601String(),
            ] : null,
            $this->normalizeUrl($business->website),
            $this->normalizeUrl($socialLinks['twitter'] ?? $socialLinks['x'] ?? null),
        );

        $communities = Community::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'flag_emoji']);

        return response()
            ->json([
                'data' => $resource->resolve(),
                'meta' => [
                    'communities' => $communities->map(fn ($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'slug' => $c->slug,
                        'flag_emoji' => $c->flag_emoji,
                    ])->values(),
                ],
            ])
            ->header('Cache-Control', 'private, no-cache');
    }

    public function storeReview(StoreBusinessReviewRequest $request, string $slug): JsonResponse
    {
        $business = $this->findActiveBusiness($slug);
        $userId = (int) $request->user()->id;

        if (BusinessReview::blocksNewSubmission($business->id, $userId)) {
            return response()->json([
                'message' => 'You have already reviewed this business or have a review awaiting approval.',
                'code' => 'already_reviewed',
            ], 422);
        }

        $communityId = $request->filled('community_id')
            ? (int) $request->input('community_id')
            : null;

        $attributes = [
            'business_id' => $business->id,
            'user_id' => $userId,
            'rating' => (int) $request->input('rating'),
            'body' => trim((string) $request->input('body')),
            'community_id' => $communityId,
            'status' => BusinessReview::STATUS_PENDING,
        ];

        if ($request->hasFile('image')) {
            $attributes['image_url'] = $this->storeReviewImage(
                $request->file('image'),
                $business,
                $userId,
            );
        }

        $review = DB::transaction(function () use ($attributes) {
            $review = BusinessReview::query()->create($attributes);
            $review->load(['user:id,name,username,email', 'community:id,name,slug,flag_emoji']);

            return $review;
        });

        $message = $communityId !== null
            ? 'Your review has been submitted for approval. After it is published, +5 points will be added to the selected community.'
            : 'Your review has been submitted and is awaiting admin approval.';

        return response()->json([
            'message' => $message,
            'data' => (new BusinessReviewResource($review))->resolve(),
        ], 201);
    }

    public function storeVerification(StoreVerificationRequest $request, string $slug): JsonResponse
    {
        $business = $this->findActiveBusiness($slug);
        $validated = $request->validated();
        $userId = (int) $request->user()->id;

        $existingPending = VerificationRequest::query()
            ->where('business_id', $business->id)
            ->where('user_id', $userId)
            ->where('status', VerificationRequest::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return response()->json([
                'message' => 'You already have a pending verification request for this business.',
                'code' => 'pending_verification',
            ], 422);
        }

        VerificationRequest::query()->create([
            'business_id' => $business->id,
            'user_id' => $userId,
            'outdated_info' => $validated['outdated_info'] ?? null,
            'how_verified' => trim($validated['how_verified']),
            'status' => VerificationRequest::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Verification request submitted. Our team will review it soon.',
            'data' => ['has_pending_verification' => true],
        ], 201);
    }

    private function findActiveBusiness(string $slug): Business
    {
        return Business::query()
            ->where('slug', $slug)
            ->where('status', Business::STATUS_ACTIVE)
            ->firstOrFail();
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
}
