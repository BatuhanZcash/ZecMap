<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Zero to Shielded · Zcash Wallet Guide — {{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => 'Zero to Shielded · Zcash Wallet Guide',
            'description' => 'A step by step Zcash wallet guide with real screens: install Zodl, Vizor, Noir or Zingo, back up your 24 words, get ZEC, shield it and send your first private transaction.',
            'image' => asset('guides/zero-to-shielded/og-image.png'),
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
                    {{-- If the ZecMap header is sticky, this keeps the wallet tabs below it.
                         The hero top padding clears the floating theme / language pill. --}}
                    <style>
                        .zts{--sticky-top:96px}
                        /* Clear the floating theme / language pill that hangs below the ZecMap header. */
                        #top-zts .hero{padding-top:96px}
                        @media (max-width:860px){#top-zts .hero{padding-top:64px}}
                    </style>
                    @include('guides.partials.zero-to-shielded')

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
