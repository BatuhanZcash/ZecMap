<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Business */
class BrowseBusinessResource extends JsonResource
{
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
            'lat' => $business->lat,
            'lng' => $business->lng,
            'address' => $business->address,
            'city' => $business->city,
            'verified' => $business->verified,
            'logo_url' => $business->logoDisplayUrl(),
            'view_count' => $business->view_count,
            'created_at' => $business->created_at?->toIso8601String(),
            'website' => $this->normalizeUrl($business->website),
            'twitter_url' => $this->normalizeUrl(is_string($twitterRaw) ? $twitterRaw : null),
            'cover_class' => $this->coverClassForCategory($category?->slug),
            'is_new' => $business->created_at?->greaterThan(now()->subDays(30)) ?? false,
            'badge' => $business->browseBadge(),
            'categories' => $category ? [
                'slug' => $category->slug,
                'name' => $category->name,
                'icon' => null,
            ] : null,
        ];
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return 'https://'.$url;
        }

        return $url;
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
