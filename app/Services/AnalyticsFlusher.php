<?php

namespace App\Services;

use App\Models\AnalyticsDailyStat;
use App\Models\AnalyticsDimensionStat;
use Illuminate\Support\Facades\DB;

class AnalyticsFlusher
{
    public function __construct(
        private readonly AnalyticsRecorder $recorder,
    ) {}

    public function flush(?string $date = null): int
    {
        $dates = $date !== null
            ? [$date]
            : array_values(array_unique(array_merge(
                $this->recorder->dirtyDates(),
                $this->recentDates(14),
            )));

        if ($dates === []) {
            return 0;
        }

        $flushed = 0;

        foreach ($dates as $statDate) {
            if ($this->flushDate($statDate)) {
                $flushed++;
            }
        }

        return $flushed;
    }

    private function flushDate(string $date): bool
    {
        $dailyCounts = $this->recorder->pendingDailyCounts($date);
        $dimensionCounts = $this->recorder->pendingDimensionCounts($date);

        $hasPending = array_sum($dailyCounts) > 0
            || collect($dimensionCounts)->flatten()->sum() > 0;

        if (! $hasPending) {
            $this->recorder->clearDirtyDate($date);

            return false;
        }

        DB::transaction(function () use ($date, $dailyCounts, $dimensionCounts): void {
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

            if ($dailyCounts['page_views'] > 0) {
                $daily->increment('page_views', $dailyCounts['page_views']);
            }

            if ($dailyCounts['sessions'] > 0) {
                $daily->increment('sessions', $dailyCounts['sessions']);
            }

            if ($dailyCounts['business_views'] > 0) {
                $daily->increment('business_views', $dailyCounts['business_views']);
            }

            foreach ($dimensionCounts as $dimension => $items) {
                foreach ($items as $dimensionKey => $hits) {
                    if ($hits <= 0) {
                        continue;
                    }

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

                    $row->increment('hits', $hits);
                }
            }
        });

        $this->recorder->clearPending($date);
        $this->recorder->clearDirtyDate($date);

        return true;
    }

    /**
     * @return list<string>
     */
    private function recentDates(int $days): array
    {
        $dates = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $dates[] = today()->subDays($offset)->toDateString();
        }

        return $dates;
    }
}
