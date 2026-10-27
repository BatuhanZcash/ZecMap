<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if ($isPending)
        <meta name="robots" content="noindex">
    @endif

    <title>{{ $business->name }} — {{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => $business->name . ' — ' . config('app.name', 'ZecMap'),
            'description' => $business->description
                ? \Illuminate\Support\Str::limit(strip_tags($business->description), 200)
                : __('browse.subtitle'),
            'image' => $business->logoDisplayUrl(),
        ],
    ])

    @include('layouts.partials.app-styles')
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')

            <main class="app-main">
                <div data-page-scroll="true" class="page-scroll">
                    <div class="biz-page-shell">
                        <div class="biz-page-max-width">
                            @if (session('success'))
                                <div class="biz-page-flash biz-page-flash--success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="biz-page-flash biz-page-flash--error">{{ session('error') }}</div>
                            @endif
                            @if ($isPending)
                                <div class="biz-page-flash biz-page-flash--pending" style="background: rgba(243, 184, 38, .12); border: 1px solid rgba(243, 184, 38, .32); color: inherit;">
                                    This listing is awaiting review, so some details may still change. You can suggest
                                    corrections with the “Update this business” button.
                                </div>
                            @endif
                            @include('business-detail.partials.content')
                        </div>
                    </div>

                    @include('layouts.footer')
                </div>
            </main>

            @include('layouts.partials.mobile-bottom-nav')
        </div>

        <div class="toast-stack"></div>
    </div>

    @include('business-detail.partials.share-sheet')

    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/business-share.js') }}" defer></script>
    @auth
        @include('business-detail.partials.verify-modal')
        @if ($canRequestUpdate)
            @include('business-detail.partials.update-modal')
        @endif
        <script src="{{ asset('assets/js/business-reviews.js') }}" defer></script>
        <script src="{{ asset('assets/js/business-verify.js') }}" defer></script>
    @endauth
</body>

</html>
