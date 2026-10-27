<?php

namespace App\Services;

use App\Models\AnalyticsDailyStat;
use App\Models\AnalyticsDimensionStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsRecorder
{
    public function __construct(
        private readonly VisitorCountryResolver $countryResolver,
    ) {}

    private const CACHE_PREFIX = 'analytics:pending:';

    private const DIRTY_DATES_KEY = 'analytics:dirty_dates';

    private const DAILY_VISIT_FLAG_PREFIX = 'analytics.daily_visit.';

    public function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->is('admin', 'admin/*', 'up', 'api', 'api/*')) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        $path = $request->path();

        if (str_starts_with($path, 'build/') || str_contains($path, '.')) {
            return false;
        }

        $userAgent = (string) $request->userAgent();

        return ! $this->isBot($userAgent);
    }

    public function recordPageView(Request $request): bool
    {
        if (! $this->shouldTrack($request)) {
            return false;
        }

        $date = now()->toDateString();

        if ($request->session()->get($this->dailyVisitFlag($date))) {
            return false;
        }

        $device = $this->detectDevice((string) $request->userAgent());
        $country = $this->countryResolver->resolve($request);

        DB::transaction(function () use ($date, $device, $country): void {
            $this->persistDailyIncrement($date, 'page_views');
            $this->persistDailyIncrement($date, 'sessions');
            $this->persistDimensionIncrement($date, AnalyticsDimensionStat::DIMENSION_DEVICE, $device);

            if ($country !== null) {
                $this->persistDimensionIncrement($date, AnalyticsDimensionStat::DIMENSION_COUNTRY, $country);
            }
        });

        $request->session()->put($this->dailyVisitFlag($date), true);

        return true;
    }

    public function recordBusinessView(Request $request): void
    {
        if (! $this->shouldTrackBusinessView($request)) {
            return;
        }

        $this->persistDailyIncrement(now()->toDateString(), 'business_views');
    }

    /**
     * @return array<string, int>
     */
    public function pendingDailyCounts(string $date): array
    {
        return [
            'page_views' => $this->readLegacyCounter($date, 'page_views'),
            'sessions' => $this->readLegacyCounter($date, 'sessions'),
            'business_views' => $this->readLegacyCounter($date, 'business_views'),
        ];
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function pendingDimensionCounts(string $date): array
    {
        $devices = [];
        foreach (['desktop', 'mobile', 'tablet'] as $device) {
            $count = $this->readLegacyCounter($date, 'device', $device);
            if ($count > 0) {
                $devices[$device] = $count;
            }
        }

        $countries = [];

        foreach ($this->knownLegacyCountryKeys($date) as $countryCode) {
            $count = $this->readLegacyCounter($date, 'country', $countryCode);
            if ($count > 0) {
                $countries[$countryCode] = $count;
            }
        }

        return [
            'device' => $devices,
            'country' => $countries,
        ];
    }

    /**
     * @return list<string>
     */
    public function dirtyDates(): array
    {
        $dates = Cache::get(self::DIRTY_DATES_KEY, []);

        return is_array($dates) ? array_values(array_unique($dates)) : [];
    }

    public function clearDirtyDate(string $date): void
    {
        $dates = array_values(array_filter(
            $this->dirtyDates(),
            fn (string $dirtyDate) => $dirtyDate !== $date,
        ));

        if ($dates === []) {
            Cache::forget(self::DIRTY_DATES_KEY);

            return;
        }

        Cache::put(self::DIRTY_DATES_KEY, $dates, now()->addDays(30));
    }

    public function clearPending(string $date): void
    {
        Cache::forget($this->legacyCounterKey($date, 'page_views'));
        Cache::forget($this->legacyCounterKey($date, 'sessions'));
        Cache::forget($this->legacyCounterKey($date, 'business_views'));

        foreach (['desktop', 'mobile', 'tablet'] as $device) {
            Cache::forget($this->legacyCounterKey($date, 'device', $device));
        }

        foreach ($this->knownLegacyCountryKeys($date) as $countryCode) {
            Cache::forget($this->legacyCounterKey($date, 'country', $countryCode));
        }

        Cache::forget(self::CACHE_PREFIX.$date.':country_keys');
    }

    public function detectDevice(string $userAgent): string
    {
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent) === 1) {
            return 'tablet';
        }

        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent) === 1) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function persistDailyIncrement(string $date, string $column): void
    {
        $daily = AnalyticsDailyStat::query()
            ->lockForUpdate()
            ->firstOrCreate(
                ['stat_date' => $date],
                [
                    'page_views' => 0,
                    'sessions' => 0,
                    'business_views' => 0,
                ],
            );

        $daily->increment($column);
    }

    private function persistDimensionIncrement(string $date, string $dimension, string $dimensionKey): void
    {
        $row = AnalyticsDimensionStat::query()
            ->lockForUpdate()
            ->firstOrCreate(
                [
                    'stat_date' => $date,
                    'dimension' => $dimension,
                    'dimension_key' => $dimensionKey,
                ],
                ['hits' => 0],
            );

        $row->increment('hits');
    }

    private function shouldTrackBusinessView(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        return ! $this->isBot((string) $request->userAgent());
    }

    private function dailyVisitFlag(string $date): string
    {
        return self::DAILY_VISIT_FLAG_PREFIX.$date;
    }

    private function readLegacyCounter(string $date, string $metric, ?string $dimensionKey = null): int
    {
        return (int) Cache::get($this->legacyCounterKey($date, $metric, $dimensionKey), 0);
    }

    private function legacyCounterKey(string $date, string $metric, ?string $dimensionKey = null): string
    {
        if ($dimensionKey === null) {
            return self::CACHE_PREFIX.$date.':'.$metric;
        }

        return self::CACHE_PREFIX.$date.':'.$metric.':'.$dimensionKey;
    }

    /**
     * @return list<string>
     */
    private function knownLegacyCountryKeys(string $date): array
    {
        $countries = Cache::get(self::CACHE_PREFIX.$date.':country_keys', []);

        return is_array($countries) ? $countries : [];
    }

    private function isBot(string $userAgent): bool
    {
        if ($userAgent === '') {
            return true;
        }

        return preg_match('/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|whatsapp|preview/i', $userAgent) === 1;
    }
}
