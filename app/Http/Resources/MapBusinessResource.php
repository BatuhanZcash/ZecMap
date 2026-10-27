<?php

namespace App\Http\Resources;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Business */
class MapBusinessResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $business = $this->resource;

        return [
            'id' => $business->id,
            'slug' => $business->slug,
            'name' => $business->name,
            'lat' => $business->lat,
            'lng' => $business->lng,
            'city' => $business->city,
            'category' => $business->category?->name,
            'verified' => $business->verified,
            'logo_url' => $business->logoDisplayUrl(),
            'description' => $business->description,
            'address' => $business->address,
            'phone' => $business->phone,
            'website' => $business->website,
            'payment_provider' => $business->payment_provider ?? Business::PAYMENT_ZCASH,
            'reviews_count' => (int) ($business->reviews_count ?? 0),
            'google_maps_directions_url' => $business->googleMapsDirectionsUrl(),
            'detail_url' => url('/business/'.$business->slug),
        ];
    }
}
