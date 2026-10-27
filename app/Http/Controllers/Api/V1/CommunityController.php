<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunityLeaderboardResource;
use App\Models\Community;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommunityController extends Controller
{
    /**
     * @var list<string>
     */
    private const PERIODS = ['weekly', 'monthly', 'yearly', 'all'];

    /**
     * @var array<string, string>
     */
    private const PERIOD_ALIASES = [
        'week' => 'weekly',
        'month' => 'monthly',
        'year' => 'yearly',
        'weekly' => 'weekly',
        'monthly' => 'monthly',
        'yearly' => 'yearly',
        'all' => 'all',
    ];

    public function leaderboard(Request $request): JsonResponse
    {
        $period = $this->resolvePeriod($request->query('period', 'monthly'));
        $ranked = $this->rankedCommunities($period);

        return response()
            ->json([
                'data' => CommunityLeaderboardResource::collection($ranked),
                'meta' => [
                    'period' => $period,
                    'count' => $ranked->count(),
                ],
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * @return Collection<int, array{model: Community, scores: array<string, int>, rank: int}>
     */
    private function rankedCommunities(string $period): Collection
    {
        return Community::query()
            ->orderBy('name')
            ->get()
            ->map(function (Community $community) {
                return [
                    'model' => $community,
                    'scores' => $community->scores(),
                ];
            })
            ->sortByDesc(fn (array $row) => $row['scores'][$period])
            ->values()
            ->map(function (array $row, int $index) use ($period) {
                $row['rank'] = $index + 1;
                $row['period'] = $period;

                return $row;
            });
    }

    private function resolvePeriod(?string $period): string
    {
        $normalized = self::PERIOD_ALIASES[$period ?? ''] ?? $period;

        return in_array($normalized, self::PERIODS, true) ? $normalized : 'monthly';
    }
}
