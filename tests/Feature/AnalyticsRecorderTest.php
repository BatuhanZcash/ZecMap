<?php

namespace Tests\Feature;

use App\Models\AnalyticsDailyStat;
use App\Services\AnalyticsRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AnalyticsRecorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_views_are_persisted_to_database(): void
    {
        $recorder = app(AnalyticsRecorder::class);
        $request = Request::create('/', 'GET', server: [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
        ]);
        $request->setLaravelSession(app('session.store'));

        $recorder->recordPageView($request);
        $recorder->recordPageView($request);

        $row = AnalyticsDailyStat::query()
            ->whereDate('stat_date', today())
            ->first();

        $this->assertNotNull($row);
        $this->assertSame(1, $row->page_views);
        $this->assertSame(1, $row->sessions);
    }

    public function test_page_views_reset_on_a_new_day(): void
    {
        $recorder = app(AnalyticsRecorder::class);
        $request = Request::create('/', 'GET', server: [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
        ]);
        $request->setLaravelSession(app('session.store'));

        $this->travelTo(today()->subDay());
        $recorder->recordPageView($request);

        $this->travelBack();
        $recorder->recordPageView($request);

        $this->assertSame(1, AnalyticsDailyStat::query()->whereDate('stat_date', today()->subDay())->value('page_views'));
        $this->assertSame(1, AnalyticsDailyStat::query()->whereDate('stat_date', today())->value('page_views'));
    }

    public function test_dashboard_reads_historical_page_views_from_database(): void
    {
        AnalyticsDailyStat::query()->create([
            'stat_date' => today()->subDays(2)->toDateString(),
            'page_views' => 42,
            'sessions' => 10,
            'business_views' => 3,
        ]);

        $response = $this->get('/analytics?period=7');

        $response->assertOk();
        $response->assertSee('42');
    }
}
