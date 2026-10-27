<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Accept ZEC</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.acceptZec') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('acceptZec.subtitle'),
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
                    <div class="browse-page accept-zec-page browse-page-shell">
                        <div class="browse-container">
                            <header class="accept-zec-header">
                                <h1 class="browse-title">Accept ZEC</h1>
                                <p class="browse-subtitle">Online services and tools that accept Zcash payments.</p>
                            </header>
                            @if ($sites->isEmpty())
                                <p class="browse-empty">No services listed yet. Check back soon.</p>
                            @else
                                <div class="browse-grid">
                                    @foreach ($sites as $site)
                                        @include('accept-zec.partials.card', ['site' => $site])
                                    @endforeach
                                </div>
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
</body>

</html>
