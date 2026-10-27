<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $post->title }} — {{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => $post->title . ' — ' . config('app.name', 'ZecMap'),
            'description' => $post->displayExcerpt(),
            'image' => $post->featuredImageUrl(),
            'type' => 'article',
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
                    <div class="blog-page browse-page-shell">
                        <div class="blog-container blog-detail-container">
                            @include('blog-detail.partials.content')
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
