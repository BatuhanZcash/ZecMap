<?php

namespace App\Services;

use App\Models\AnalyticsDailyStat;
use App\Models\AnalyticsDimensionStat;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\Category;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard
{
    public const PERIOD_OPTIONS = [7, 30, 90];

    public static function forgetDashboardCache(): void
    {
        foreach (self::PERIOD_OPTIONS as $period) {
            foreach (array_keys(config('locales.supported', [])) as $locale) {
                Cache::forget('analytics:dashboard:'.$period.':'.$locale);
            }
        }
    }

    public function __construct(
        private readonly VisitorCountryResolver $countryResolver,
    ) {}

    public function get(int $periodDays = 30): array
    {
        $periodDays = $this->normalizePeriod($periodDays);

        return Cache::remember(
            'analytics:dashboard:'.$periodDays.':'.app()->getLocale(),
            now()->addSeconds(30),
            fn () => $this->build($periodDays),
        );
    }

    private function build(int $periodDays): array
    {
        $endDate = today();
        $startDate = today()->subDays($periodDays - 1);

        $dailyRows = AnalyticsDailyStat::query()
            ->whereBetween('stat_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('stat_date')
            ->get()
            ->keyBy(fn (AnalyticsDailyStat $row) => $row->stat_date->toDateString());

        $chartPoints = $this->buildChartPoints($startDate, $endDate, $dailyRows);
        $chartGeometry = $this->buildChartGeometry($chartPoints);

        $periodTotals = $this->periodTotals($startDate, $endDate, $dailyRows);

        $devices = $this->deviceBreakdown($startDate, $endDate);
        $topCountries = $this->countryBreakdown($startDate, $endDate);

        return [
            'periodDays' => $periodDays,
            'pageViews' => $periodTotals['page_views'],
            'sessions' => $periodTotals['sessions'],
            'businessViews' => $periodTotals['business_views'],
            'chartPoints' => $chartPoints,
            'chartGeometry' => $chartGeometry,
            'devices' => $devices,
            'topCountries' => $topCountries,
            'countryTotal' => max($topCountries->sum('hits'), 1),
            'directory' => $this->directorySummary(),
        ];
    }

    /**
     * @param  Collection<string, AnalyticsDailyStat>  $dailyRows
     * @return array{page_views: int, sessions: int, business_views: int}
     */
    private function periodTotals(Carbon $startDate, Carbon $endDate, Collection $dailyRows): array
    {
        $totals = [
            'page_views' => 0,
            'sessions' => 0,
            'business_views' => 0,
        ];

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $key = $date->toDateString();
            $row = $dailyRows->get($key);

            $totals['page_views'] += (int) ($row->page_views ?? 0);
            $totals['sessions'] += (int) ($row->sessions ?? 0);
            $totals['business_views'] += (int) ($row->business_views ?? 0);
        }

        return $totals;
    }

    /**
     * @param  Collection<string, AnalyticsDailyStat>  $dailyRows
     * @return list<array{label: string, value: int}>
     */
    private function buildChartPoints(Carbon $startDate, Carbon $endDate, Collection $dailyRows): array
    {
        $points = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        $dayCount = $period->count();
        $labelEvery = max(1, (int) floor($dayCount / 8));

        $index = 0;
        foreach ($period as $date) {
            $key = $date->toDateString();
            $value = (int) ($dailyRows->get($key)?->page_views ?? 0);

            $showLabel = $index === 0 || $index === $dayCount - 1 || $index % $labelEvery === 0;

            $points[] = [
                'label' => $showLabel ? $date->translatedFormat('j M') : '',
                'tooltipLabel' => $date->translatedFormat('j F Y'),
                'value' => $value,
            ];

            $index++;
        }

        return $points;
    }

    /**
     * @param  list<array{label: string, tooltipLabel: string, value: int}>  $chartPoints
     */
    private function buildChartGeometry(array $chartPoints): array
    {
        $values = array_column($chartPoints, 'value');
        $chartMax = max($values ?: [0]);
        $chartMin = 0;
        $chartPlotWidth = 936;
        $chartPlotHeight = 200;
        $chartPaddingLeft = 65;
        $chartPaddingTop = 5;
        $chartCount = count($chartPoints);

        $coords = [];
        foreach ($chartPoints as $index => $point) {
            $x = $chartPaddingLeft + ($chartCount > 1 ? ($index / ($chartCount - 1)) * $chartPlotWidth : $chartPlotWidth / 2);
            $ratio = $chartMax > $chartMin ? ($point['value'] - $chartMin) / ($chartMax - $chartMin) : 0;
            $y = $chartPaddingTop + $chartPlotHeight - ($ratio * $chartPlotHeight);
            $coords[] = [
                'x' => round($x, 1),
                'y' => round($y, 1),
                'label' => $point['label'],
                'tooltipLabel' => $point['tooltipLabel'],
                'value' => $point['value'],
            ];
        }

        if ($chartCount === 0) {
            return [
                'coords' => [],
                'linePath' => '',
                'areaPath' => '',
                'yTicks' => [0, 0, 0, 0, 0],
                'chartMax' => 0,
                'chartMin' => 0,
                'chartPaddingLeft' => $chartPaddingLeft,
                'chartPaddingTop' => $chartPaddingTop,
                'chartPlotHeight' => $chartPlotHeight,
            ];
        }

        $linePath = collect($coords)
            ->map(fn (array $coord, int $index) => ($index === 0 ? 'M' : 'L').$coord['x'].','.$coord['y'])
            ->join(' ');

        $areaPath = $linePath
            .' L'.$coords[$chartCount - 1]['x'].','.($chartPaddingTop + $chartPlotHeight)
            .' L'.$coords[0]['x'].','.($chartPaddingTop + $chartPlotHeight)
            .' Z';

        $yTicks = [];
        for ($tickIndex = 0; $tickIndex <= 4; $tickIndex++) {
            $yTicks[] = (int) round($chartMin + (($chartMax - $chartMin) * (4 - $tickIndex) / 4));
        }

        return [
            'coords' => $coords,
            'linePath' => $linePath,
            'areaPath' => $areaPath,
            'yTicks' => $yTicks,
            'chartMax' => $chartMax,
            'chartMin' => $chartMin,
            'chartPaddingLeft' => $chartPaddingLeft,
            'chartPaddingTop' => $chartPaddingTop,
            'chartPlotHeight' => $chartPlotHeight,
        ];
    }

    /**
     * @return list<array{label: string, percent: int}>
     */
    private function deviceBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        $rows = AnalyticsDimensionStat::query()
            ->select('dimension_key', DB::raw('SUM(hits) as total_hits'))
            ->where('dimension', AnalyticsDimensionStat::DIMENSION_DEVICE)
            ->whereBetween('stat_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('dimension_key')
            ->pluck('total_hits', 'dimension_key')
            ->all();

        $total = max(array_sum($rows), 1);
        $order = ['desktop', 'mobile', 'tablet'];

        return collect($order)
            ->map(function (string $device) use ($rows, $total) {
                $hits = (int) ($rows[$device] ?? 0);

                return [
                    'label' => $device,
                    'percent' => (int) round(($hits / $total) * 100),
                ];
            })
            ->all();
    }

    /**
     * @return Collection<int, object{country_code: string, hits: int}>
     */
    private function countryBreakdown(Carbon $startDate, Carbon $endDate): Collection
    {
        $rows = AnalyticsDimensionStat::query()
            ->select('dimension_key as country_code', DB::raw('SUM(hits) as hits'))
            ->where('dimension', AnalyticsDimensionStat::DIMENSION_COUNTRY)
            ->whereBetween('stat_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('dimension_key')
            ->orderByDesc('hits')
            ->get()
            ->keyBy('country_code');

        return $rows
            ->sortByDesc(fn (object $row) => (int) $row->hits)
            ->take(8)
            ->values()
            ->map(function (object $row): object {
                $row->hits = (int) $row->hits;
                $row->country_name = $this->countryResolver->countryName($row->country_code);

                return $row;
            });
    }

    private function directorySummary(): array
    {
        $activeBusinesses = Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->count();

        $verifiedBusinesses = Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->where('verified', true)
            ->count();

        $categoryCount = Category::query()->count();
        $reviewCount = BusinessReview::query()->approved()->count();

        $topCategories = Category::query()
            ->withCount([
                'businesses' => fn ($query) => $query->where('status', Business::STATUS_ACTIVE),
            ])
            ->orderByDesc('businesses_count')
            ->limit(5)
            ->get()
            ->filter(fn (Category $category) => $category->businesses_count > 0);

        return [
            'activeBusinesses' => $activeBusinesses,
            'verifiedBusinesses' => $verifiedBusinesses,
            'categoryCount' => $categoryCount,
            'reviewCount' => $reviewCount,
            'topCategories' => $topCategories,
        ];
    }

    private function normalizePeriod(int $periodDays): int
    {
        return in_array($periodDays, self::PERIOD_OPTIONS, true) ? $periodDays : 30;
    }
}
