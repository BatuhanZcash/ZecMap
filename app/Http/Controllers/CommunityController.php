<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CommunityController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * @var list<string>
     */
    private const PERIODS = ['weekly', 'monthly', 'yearly', 'all'];

    public function show(Community $community): View
    {
        [$businesses, $businessCount] = $community->businessesInRegion(12);

        return view('communities.show', [
            'community' => $community,
            'businesses' => $businesses,
            'businessCount' => $businessCount,
        ]);
    }

    public function mapView(): View
    {
        // Country centroids for every country (generated from Natural Earth).
        $meta = [];
        $metaPath = public_path('assets/vendor/country-meta.json');
        if (is_file($metaPath)) {
            $meta = json_decode((string) file_get_contents($metaPath), true) ?: [];
        }

        // Every country community with at least one local business gets a pin,
        // placed by its centroid. Region communities (Europe, Global) and
        // countries with no local businesses stay off the map.
        $pins = [];
        foreach (Community::query()->whereNotNull('country_code')->get() as $c) {
            $m = $meta[$c->country_code] ?? null;
            if (! $m) {
                continue;
            }

            $count = \App\Models\Business::where('country_code', $c->country_code)
                ->where('status', \App\Models\Business::STATUS_ACTIVE)
                ->where(function ($q) {
                    $q->where('payment_provider', '!=', \App\Models\Business::PAYMENT_FLEXA)
                        ->orWhereNull('payment_provider');
                })->count();

            if ($count < 1) {
                continue;
            }

            $pins[] = [
                'slug' => $c->slug,
                'name' => $c->name,
                'flag' => $c->flag_emoji ?: ($m['flag'] ?? ''),
                'lat' => $m['lat'],
                'lng' => $m['lng'],
                'count' => $count,
                'url' => route('communities.show', $c->slug),
            ];
        }

        return view('communities.map', ['pins' => $pins]);
    }

    private function periodLabels(): array
    {
        return [
            'weekly' => __('communities.periodWeek'),
            'monthly' => __('communities.periodMonth'),
            'yearly' => __('communities.periodYear'),
            'all' => __('communities.periodAll'),
        ];
    }

    public function index(Request $request): View
    {
        $period = $this->resolvePeriod($request->query('period', 'monthly'));
        $periodLabels = $this->periodLabels();
        $ranked = $this->rankedCommunities($period);
        $communities = $ranked->take(self::PER_PAGE);
        $totalCount = $ranked->count();

        return view('communities', [
            'communities' => $communities,
            'period' => $period,
            'periodLabels' => $periodLabels,
            'primaryPeriodLabel' => $periodLabels[$period] ?? __('communities.periodMonth'),
            'totalCount' => $totalCount,
            'hasMore' => $totalCount > self::PER_PAGE,
        ]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $period = $this->resolvePeriod($request->query('period', 'monthly'));
        $offset = max(0, (int) $request->query('offset', 0));
        $ranked = $this->rankedCommunities($period);
        $communities = $ranked->slice($offset, self::PER_PAGE)->values();
        $nextOffset = $offset + $communities->count();

        return response()->json([
            'html' => view('communities.partials.list', [
                'communities' => $communities,
                'period' => $period,
                'startRank' => $offset + 1,
            ])->render(),
            'hasMore' => $nextOffset < $ranked->count(),
            'nextOffset' => $nextOffset,
        ]);
    }

    /**
     * @return Collection<int, array{model: Community, scores: array<string, int>}>
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
            ->values();
    }

    private function resolvePeriod(?string $period): string
    {
        return in_array($period, self::PERIODS, true) ? $period : 'monthly';
    }
}
