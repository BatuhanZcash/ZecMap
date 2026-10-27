<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\AcceptZecSite */
class AcceptZecSiteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'logo_url' => $this->logoDisplayUrl(),
            'url' => $this->website_url,
            'website_host' => $this->websiteDisplayHost(),
            'sort_order' => $this->sort_order,
        ];
    }
}
