<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MapBusinessResource;
use App\Http\Resources\MapPinResource;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function pins(Request $request): JsonResponse
    {
        $query = Business::query()
            ->with('category:id,name,slug')
            ->where('status', Business::STATUS_ACTIVE)
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        $focusSlug = trim((string) $request->query('business', ''));
        $pins = $query->orderBy('name')->get();

        $meta = [
            'count' => $pins->count(),
            'focus_slug' => null,
        ];

        if ($focusSlug !== '') {
            $focused = $pins->firstWhere('slug', $focusSlug);
            if ($focused) {
                $meta['focus_slug'] = $focused->slug;
                $meta['focus'] = [
                    'lat' => $focused->lat,
                    'lng' => $focused->lng,
                    'zoom' => 14,
                ];
            }
        }

        return response()
            ->json([
                'data' => MapPinResource::collection($pins)->resolve(),
                'meta' => $meta,
            ])
            ->header('Cache-Control', 'public, max-age=120');
    }

    public function business(Business $business): JsonResponse
    {
        abort_unless($business->isActive(), 404);
        abort_unless($business->lat !== null && $business->lng !== null, 404);

        $business->load('category:id,name');
        $business->loadCount('reviews');

        return response()
            ->json([
                'data' => (new MapBusinessResource($business))->resolve(),
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }
}
