<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcceptZecSiteResource;
use App\Models\AcceptZecSite;
use Illuminate\Http\JsonResponse;

class AcceptZecController extends Controller
{
    public function index(): JsonResponse
    {
        $sites = AcceptZecSite::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()
            ->json([
                'data' => AcceptZecSiteResource::collection($sites),
                'meta' => [
                    'count' => $sites->count(),
                ],
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }
}
