<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') — {{ config('app.name', 'ZecMap') }}</title>

    @php
        $pageTitle = trim(strip_tags((string) View::yieldContent('title')));
        $metaDescription = trim(strip_tags((string) View::yieldContent('meta_description')));
    @endphp
    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => $pageTitle !== '' ? "{$pageTitle} — " . config('app.name', 'ZecMap') : config('app.name', 'ZecMap'),
            'description' => $metaDescription !== '' ? $metaDescription : null,
            'type' => trim(strip_tags((string) View::yieldContent('meta_type'))) ?: 'website',
            'image' => trim(strip_tags((string) View::yieldContent('meta_image'))) ?: null,
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
                    <div class="static-page browse-page-shell">
                        <div class="static-page-container @yield('container-class')">
                            @yield('content')
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
