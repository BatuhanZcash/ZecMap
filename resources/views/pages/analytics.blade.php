@extends('layouts.static-page')

@section('title', __('analytics.title'))
@section('meta_description', __('seo.analytics'))
@section('container-class', 'static-page-container--wide')

@section('content')
    @php
        $chartCoords = $chartGeometry['coords'];
        $chartLinePath = $chartGeometry['linePath'];
        $chartAreaPath = $chartGeometry['areaPath'];
        $chartYTicks = $chartGeometry['yTicks'];
        $chartPaddingLeft = $chartGeometry['chartPaddingLeft'];
        $chartPaddingTop = $chartGeometry['chartPaddingTop'];
        $chartPlotHeight = $chartGeometry['chartPlotHeight'];
    @endphp

    <div class="analytics-page">
        <header class="analytics-header">
            <div class="analytics-header-text">
                <h1 class="analytics-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="analytics-title-icon" aria-hidden="true">
                        <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                        <path d="M18 17V9"></path>
                        <path d="M13 17V5"></path>
                        <path d="M8 17v-3"></path>
                    </svg>
                    {{ __('analytics.title') }}
                </h1>
                <p class="analytics-subtitle">{{ __('analytics.subtitle') }}</p>
            </div>
            <div class="analytics-periods" role="group" aria-label="{{ __('analytics.timeRange') }}">
                @foreach ([7, 30, 90] as $period)
                    <a href="{{ route('analytics', ['period' => $period]) }}"
                        class="analytics-period-btn{{ $periodDays === $period ? ' is-active' : '' }}">{{ $period }}d</a>
                @endforeach
            </div>
        </header>

        <div class="analytics-kpi-grid">
            <article class="analytics-kpi-card">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="analytics-kpi-icon" aria-hidden="true">
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <div class="analytics-kpi-value">{{ number_format($pageViews) }}</div>
                <div class="analytics-kpi-label">{{ __('analytics.pageviews') }}</div>
            </article>
            <article class="analytics-kpi-card">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="analytics-kpi-icon" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                </svg>
                <div class="analytics-kpi-value">{{ number_format($sessions) }}</div>
                <div class="analytics-kpi-label">{{ __('analytics.sessions') }}</div>
            </article>
            <article class="analytics-kpi-card">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="analytics-kpi-icon" aria-hidden="true">
                    <path d="M15 21v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5"></path>
                    <path d="M17.774 10.31a1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.451 0 1.12 1.12 0 0 0-1.548 0 2.5 2.5 0 0 1-3.452 0 1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.77-3.248l2.889-4.184A2 2 0 0 1 7 2h10a2 2 0 0 1 1.653.873l2.895 4.192a2.5 2.5 0 0 1-3.774 3.244"></path>
                    <path d="M4 10.95V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8.05"></path>
                </svg>
                <div class="analytics-kpi-value">{{ number_format($businessViews) }}</div>
                <div class="analytics-kpi-label">{{ __('analytics.businessViews') }}</div>
            </article>
        </div>

        <section class="analytics-panel analytics-panel--chart">
            <h2 class="analytics-panel-title">{{ __('analytics.dailyPageviews') }}</h2>
            <div class="analytics-chart" role="img" aria-label="{{ __('analytics.dailyPageviewsChart') }}">
                <svg class="analytics-line-chart" viewBox="0 0 1001 240" preserveAspectRatio="xMidYMid meet">
                    <defs>
                        <linearGradient id="analytics-line-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f5c542" stop-opacity="0.28"></stop>
                            <stop offset="100%" stop-color="#f5c542" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    @for ($tick = 0; $tick <= 4; $tick++)
                        @php
                            $gridY = $chartPaddingTop + ($tick / 4) * $chartPlotHeight;
                        @endphp
                        <line class="analytics-line-grid" x1="{{ $chartPaddingLeft }}" y1="{{ $gridY }}"
                            x2="1001" y2="{{ $gridY }}"></line>
                    @endfor
                    @foreach ($chartYTicks as $index => $tickValue)
                        @php
                            $labelY = $chartPaddingTop + ($index / 4) * $chartPlotHeight;
                        @endphp
                        <text class="analytics-line-y-label" x="57" y="{{ $labelY + 4 }}"
                            text-anchor="end">{{ number_format($tickValue) }}</text>
                    @endforeach
                    @if ($chartLinePath !== '')
                        <path class="analytics-line-area" d="{{ $chartAreaPath }}"></path>
                        <path class="analytics-line-path" d="{{ $chartLinePath }}"></path>
                        @foreach ($chartCoords as $coord)
                            <g class="analytics-line-point" transform="translate({{ $coord['x'] }}, {{ $coord['y'] }})">
                                <title>{{ $coord['tooltipLabel'] }}: {{ number_format($coord['value']) }} {{ __('analytics.views') }}</title>
                                <circle class="analytics-line-dot-hit" cx="0" cy="0" r="14"></circle>
                                <circle class="analytics-line-dot" cx="0" cy="0" r="3"></circle>
                                <g class="analytics-line-tooltip" transform="translate(0, -10)">
                                    <rect class="analytics-line-tooltip-bg" x="-46" y="-38" width="92" height="34" rx="6"></rect>
                                    <text class="analytics-line-tooltip-date" x="0" y="-23" text-anchor="middle">{{ $coord['tooltipLabel'] }}</text>
                                    <text class="analytics-line-tooltip-value" x="0" y="-8" text-anchor="middle">{{ number_format($coord['value']) }}</text>
                                </g>
                            </g>
                        @endforeach
                    @endif
                    @foreach ($chartCoords as $coord)
                        @if ($coord['label'] !== '')
                            <text class="analytics-line-x-label" x="{{ $coord['x'] }}" y="228"
                                text-anchor="middle">{{ $coord['label'] }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </section>

        <div class="analytics-split">
            <section class="analytics-panel">
                <h2 class="analytics-panel-title">{{ __('analytics.devices') }}</h2>
                <ul class="analytics-progress-list">
                    @foreach ($devices as $device)
                        <li class="analytics-progress-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="analytics-progress-icon" aria-hidden="true">
                                @if ($device['label'] === 'mobile')
                                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect>
                                    <path d="M12 18h.01"></path>
                                @elseif ($device['label'] === 'tablet')
                                    <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
                                    <line x1="12" x2="12.01" y1="18" y2="18"></line>
                                @else
                                    <rect width="20" height="14" x="2" y="3" rx="2"></rect>
                                    <line x1="8" x2="16" y1="21" y2="21"></line>
                                    <line x1="12" x2="12" y1="17" y2="21"></line>
                                @endif
                            </svg>
                            <div class="analytics-progress-body">
                                <div class="analytics-progress-row">
                                    <span class="analytics-progress-name">{{ __('analytics.device.' . $device['label']) }}</span>
                                    <span class="analytics-progress-value">{{ $device['percent'] }}%</span>
                                </div>
                                <div class="analytics-progress-track">
                                    <div class="analytics-progress-fill"
                                        style="width: {{ $device['percent'] }}%;"></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="analytics-panel">
                <h2 class="analytics-panel-title">{{ __('analytics.countries') }}</h2>
                <ul class="analytics-country-list">
                    @forelse ($topCountries as $country)
                        @php
                            $percent = (int) round(($country->hits / $countryTotal) * 100);
                        @endphp
                        <li class="analytics-country-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="analytics-country-icon" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                                <path d="M2 12h20"></path>
                            </svg>
                            <div class="analytics-country-row">
                                <span class="analytics-country-name"
                                    title="{{ $country->country_code }}">{{ $country->country_name }}</span>
                                <span class="analytics-country-stat">
                                    {{ number_format($country->hits) }}
                                    <span class="analytics-country-percent">({{ $percent }}%)</span>
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="analytics-country-empty">{{ __('analytics.noCountryData') }}</li>
                    @endforelse
                </ul>
            </section>
        </div>

        @if ($topCategories->isNotEmpty())
            <section class="analytics-panel">
                <h2 class="analytics-panel-title">{{ __('analytics.directorySummary') }}</h2>
                <div class="analytics-directory-grid">
                    <div class="analytics-directory-stat">
                        <span class="analytics-directory-value">{{ number_format($activeBusinesses) }}</span>
                        <span class="analytics-directory-label">{{ __('analytics.activeBusinesses') }}</span>
                    </div>
                    <div class="analytics-directory-stat">
                        <span class="analytics-directory-value">{{ number_format($verifiedBusinesses) }}</span>
                        <span class="analytics-directory-label">{{ __('analytics.verified') }}</span>
                    </div>
                    <div class="analytics-directory-stat">
                        <span class="analytics-directory-value">{{ number_format($categoryCount) }}</span>
                        <span class="analytics-directory-label">{{ __('analytics.category') }}</span>
                    </div>
                    <div class="analytics-directory-stat">
                        <span class="analytics-directory-value">{{ number_format($reviewCount) }}</span>
                        <span class="analytics-directory-label">{{ __('analytics.review') }}</span>
                    </div>
                </div>
                <ul class="analytics-category-list">
                    @foreach ($topCategories as $category)
                        <li class="analytics-category-item">
                            <span>{{ $category->name }}</span>
                            <span class="static-list-badge">{{ number_format($category->businesses_count) }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection
