<?php

namespace App\Http\Resources;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Business */
class MapPinResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->category;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'verified' => $this->verified,
            'logo_url' => $this->logoDisplayUrl(),
            'payment_provider' => $this->payment_provider ?? Business::PAYMENT_ZCASH,
            'categories' => $category ? [
                'slug' => $category->slug,
                'name' => $category->name,
            ] : null,
        ];
    }
}
