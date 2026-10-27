<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Blog</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.blog') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('blog.subtitle'),
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
                        <div class="blog-container">
                            <header class="blog-header">
                                <h1 class="blog-title">Latest Updates</h1>
                                <p class="blog-subtitle">News, guides, and updates from the ZecMap community.</p>
                            </header>

                            @if ($posts->isEmpty())
                                <p class="blog-empty">No blog posts yet. Check back soon.</p>
                            @else
                                <div class="blog-posts">
                                    @foreach ($posts as $post)
                                        @include('blog.partials.card', [
                                            'post' => $post,
                                            'isLatest' => $loop->first,
                                        ])
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
