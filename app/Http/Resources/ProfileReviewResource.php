<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BusinessReview */
class ProfileReviewResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $business = $this->business;

        return array_merge(
            (new BusinessReviewResource($this))->resolve(),
            [
                'business' => $business ? [
                    'id' => $business->id,
                    'slug' => $business->slug,
                    'name' => $business->name,
                    'logo_url' => $business->logoDisplayUrl(),
                    'category' => $business->category?->name,
                ] : null,
            ]
        );
    }
}
