<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BusinessReview */
class BusinessReviewResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $community = $this->community;

        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'body' => $this->body,
            'image_url' => $this->imageDisplayUrl(),
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => $user ? [
                'id' => $user->id,
                'username' => $user->username,
                'initials' => $user->initials(),
            ] : null,
            'community' => $community ? [
                'id' => $community->id,
                'name' => $community->name,
                'slug' => $community->slug,
                'flag_emoji' => $community->flag_emoji,
            ] : null,
            'status' => $this->status,
            'is_pending' => $this->isPending(),
            'is_own' => $request->user()?->id === $this->user_id,
        ];
    }
}
