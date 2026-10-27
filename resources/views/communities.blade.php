<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Communities</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.communities') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('communities.subtitle'),
        ],
    ])

    @include('layouts.partials.app-styles')
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')
            <main class="app-main">
                <div class="page-scroll" data-page-scroll="true">
                    <div class="communities-page browse-page-shell">
                        <div class="communities-container">
                            <header class="communities-header">
                                <div>
                                    <h1 class="communities-title">{{ __('communities.title') }}</h1>
                                    <p class="communities-subtitle">{{ __('communities.subtitle') }}</p>
                                </div>
                                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                                    <a class="communities-add-link" href="{{ route('communities.map') }}"
                                        style="background:transparent;border:1px solid rgba(243,184,38,.5);color:#C8921A">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 18.5a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13Z"></path>
                                            <path d="M2 12h3M19 12h3M12 2v3M12 19v3"></path>
                                        </svg>
                                        World map
                                    </a>
                                    <a class="communities-add-link" href="{{ url('/add') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-plus" aria-hidden="true">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                        {{ __('nav.addListing') }}
                                    </a>
                                </div>
                            </header>

                            <div class="communities-period-tabs" role="tablist" aria-label="{{ __('communities.periodLabel') }}">
                                @foreach ($periodLabels as $key => $label)
                                    <a href="{{ route('communities', ['period' => $key]) }}" role="tab"
                                        aria-selected="{{ $period === $key ? 'true' : 'false' }}"
                                        @class([
                                            'communities-period-tab',
                                            'communities-period-tab--active' => $period === $key,
                                        ])>
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>

                            @if ($communities->isEmpty())
                                <p class="communities-empty">{{ __('communities.emptyList') }}</p>
                            @else
                                <ol class="communities-list" id="communities-list">
                                    @include('communities.partials.list', [
                                        'communities' => $communities,
                                        'period' => $period,
                                        'startRank' => 1,
                                    ])
                                </ol>

                                @if ($hasMore)
                                    <div class="communities-load-more-wrap" id="communities-load-more-wrap">
                                        <button type="button" class="communities-load-more" id="communities-load-more"
                                            data-url="{{ route('communities.load-more') }}"
                                            data-period="{{ $period }}"
                                            data-offset="{{ $communities->count() }}">
                                            {{ __('communities.showMore') }}
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    @include('layouts.footer')
                </div>
            </main>

            @include('layouts.partials.mobile-bottom-nav')
        </div>

        <div class="toast-stack"></div>
    </div>

    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/communities.js') }}" defer></script>
</body>

</html>
