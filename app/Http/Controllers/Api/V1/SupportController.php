<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupporterResource;
use App\Models\SiteSetting;
use App\Models\Supporter;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    public function show(): JsonResponse
    {
        $supporters = $this->activeSupporters();

        $donationAddress = SiteSetting::getValue(
            SiteSetting::KEY_DONATION_ADDRESS,
            config('zecmap.donation_address'),
        );

        $shareUrl = rtrim((string) config('app.url'), '/');

        return response()
            ->json([
                'data' => [
                    'donation_address' => $donationAddress,
                    'supporters' => SupporterResource::collection($supporters)->resolve(),
                    'share_url' => $shareUrl !== '' ? $shareUrl : 'https://zecmap.com',
                    'github_url' => 'https://github.com/zecmap',
                ],
                'meta' => [
                    'supporters_count' => $supporters->count(),
                ],
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }

    public function supporters(): JsonResponse
    {
        $supporters = $this->activeSupporters();

        return response()
            ->json([
                'data' => SupporterResource::collection($supporters)->resolve(),
                'meta' => ['count' => $supporters->count()],
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Supporter>
     */
    private function activeSupporters()
    {
        return Supporter::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
