<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BusinessVerification */
class ProfileVerificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $business = $this->business;

        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toIso8601String(),
            'business' => $business ? [
                'id' => $business->id,
                'slug' => $business->slug,
                'name' => $business->name,
                'logo_url' => $business->logoDisplayUrl(),
            ] : null,
        ];
    }
}
