<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsDashboard;
use App\Services\AnalyticsRecorder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RecordPageAnalytics
{
    public function __construct(
        private readonly AnalyticsRecorder $recorder,
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->recorder->recordPageView($request)) {
            $this->forgetDashboardCache();
        }

        return $response;
    }

    private function forgetDashboardCache(): void
    {
        AnalyticsDashboard::forgetDashboardCache();
    }
}
