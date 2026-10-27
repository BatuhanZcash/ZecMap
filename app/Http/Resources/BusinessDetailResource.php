<?php

namespace App\Http\Resources;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin Business */
class BusinessDetailResource extends JsonResource
{
    /**
     * @param  Collection<int, \App\Models\BusinessReview>  $reviews
     */
    public function __construct(
        Business $business,
        private Collection $reviews,
        private ?float $averageRating,
        private ?array $userReview,
        private bool $hasPendingReview,
        private bool $hasPendingVerification,
        private ?array $lastVerification,
        private ?string $website,
        private ?string $twitterUrl,
    ) {
        parent::__construct($business);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $business = $this->resource;
        $category = $business->category;
        $socialLinks = is_array($business->social_links) ? $business->social_links : [];
        $twitterRaw = $socialLinks['twitter'] ?? $socialLinks['x'] ?? null;

        return [
            'id' => $business->id,
            'slug' => $business->slug,
            'name' => $business->name,
            'description' => $business->description,
            'address' => $business->address,
            'city' => $business->city,
            'country_code' => $business->country_code,
            'full_address' => $business->fullAddress(),
            'lat' => $business->lat,
            'lng' => $business->lng,
            'verified' => $business->verified,
            'logo_url' => $business->logoDisplayUrl(),
            'phone' => $business->phone,
            'website' => $this->website,
            'twitter_url' => $this->twitterUrl,
            'twitter_handle' => is_string($twitterRaw) ? $twitterRaw : null,
            'view_count' => $business->view_count,
            'badge' => $business->browseBadge(),
            'cover_class' => $this->coverClassForCategory($category?->slug),
            'google_maps_directions_url' => $business->googleMapsDirectionsUrl(),
            'share_url' => url('/business/'.$business->slug),
            'category' => $category ? [
                'slug' => $category->slug,
                'name' => $category->name,
            ] : null,
            'last_verification' => $this->lastVerification,
            'has_pending_verification' => $this->hasPendingVerification,
            'has_pending_review' => $this->hasPendingReview,
            'user_review' => $this->userReview,
            'reviews' => [
                'items' => BusinessReviewResource::collection($this->reviews)->resolve(),
                'average_rating' => $this->averageRating,
                'count' => $this->reviews->count(),
            ],
        ];
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
