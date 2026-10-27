<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\Category;
use App\Models\CommunityContribution;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateBusinessListing
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function execute(array $validated, ?int $userId, ?UploadedFile $logo = null): Business
    {
        $slug = $this->generateUniqueSlug($validated['name']);
        $category = ! empty($validated['category_slug'])
            ? Category::query()->where('slug', $validated['category_slug'])->first()
            : null;

        $socialLinks = [
            'email' => $validated['email'],
        ];

        $twitter = trim((string) ($validated['twitter'] ?? ''));
        if ($twitter !== '') {
            $socialLinks['twitter'] = ltrim($twitter, '@');
        }

        $telegram = trim((string) ($validated['telegram'] ?? ''));
        if ($telegram !== '') {
            $socialLinks['telegram'] = $telegram;
        }

        $attributes = [
            'slug' => $slug,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category_id' => $category?->id,
            'community_id' => $validated['community_id'] ?? null,
            'payment_provider' => $validated['payment_provider'],
            'address' => $validated['address'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'zcash_address' => $validated['zcash_address'] ?? null,
            'social_links' => $socialLinks,
            'status' => Business::STATUS_INACTIVE,
            'verified' => false,
            'owner_id' => $userId,
        ];

        if ($logo) {
            $attributes['logo_url'] = $this->storeLogo($logo, $slug);
        }

        $communityId = $validated['community_id'] ?? null;

        return DB::transaction(function () use ($attributes, $communityId, $userId) {
            $business = Business::query()->create($attributes);

            if ($communityId !== null && $userId !== null) {
                CommunityContribution::query()->create([
                    'community_id' => $communityId,
                    'points' => CommunityContribution::POINTS_BUSINESS_COMMUNITY,
                    'contribution_type' => CommunityContribution::TYPE_BUSINESS,
                    'user_id' => $userId,
                    'business_id' => $business->id,
                ]);
            }

            return $business;
        });
    }

    private function storeLogo(UploadedFile $file, string $slug): string
    {
        $directory = public_path('assets/images/business-logos');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true)) {
            $extension = match ($file->getMimeType()) {
                'image/jpeg', 'image/pjpeg' => 'jpg',
                'image/png', 'image/x-png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/bmp' => 'bmp',
                default => 'png',
            };
        }

        $filename = $slug.'-'.time().'.'.$extension;
        $file->move($directory, $filename);

        return 'assets/images/business-logos/'.$filename;
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'business';
        }

        $slug = $base;
        $counter = 2;

        while (Business::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
