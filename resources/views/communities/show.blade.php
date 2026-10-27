<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $community->name }} — {{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => $community->name . ' — ' . config('app.name', 'ZecMap'),
            'description' => $community->description
                ? \Illuminate\Support\Str::limit(strip_tags($community->description), 200)
                : 'The Zcash community in ' . $community->name . ' on ZecMap.',
        ],
    ])

    @include('layouts.partials.app-styles')
    <style>
        .zc-com-head{display:flex;align-items:center;gap:18px;margin-bottom:6px}
        .zc-com-flag{font-size:3rem;line-height:1;flex:0 0 auto}
        .zc-com-title{font-size:2rem;font-weight:800;margin:0}
        .zc-com-desc{opacity:.72;margin:10px 0 0;line-height:1.55;max-width:70ch}
        .zc-com-count{display:inline-flex;align-items:center;gap:8px;margin:18px 0 0;font-weight:700;color:#C8921A;background:rgba(244,183,40,.12);border:1px solid rgba(244,183,40,.4);padding:6px 13px;border-radius:999px;font-size:.9rem}
        .zc-biz-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin-top:4px}
        .zc-biz-card{display:flex;align-items:center;gap:13px;border:1px solid rgba(128,128,128,.18);border-radius:14px;padding:14px 16px;text-decoration:none;color:inherit;transition:border-color .15s,transform .15s}
        .zc-biz-card:hover{border-color:#F4B728;transform:translateY(-1px)}
        .zc-biz-logo{width:46px;height:46px;border-radius:12px;object-fit:cover;flex:0 0 auto;background:rgba(128,128,128,.12)}
        .zc-biz-logo-ph{width:46px;height:46px;border-radius:12px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;background:rgba(244,183,40,.15);color:#C8921A;font-weight:800}
        .zc-biz-name{font-weight:700;display:flex;align-items:center;gap:7px}
        .zc-biz-meta{opacity:.6;font-size:.82rem;margin-top:2px}
        .zc-biz-check{display:inline-flex;width:16px;height:16px;border-radius:50%;background:#35c97f;color:#052012;align-items:center;justify-content:center;font-size:10px;font-weight:800}
        .zc-empty{opacity:.6;padding:22px 0;line-height:1.6}
    </style>
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')

            <main class="app-main">
                <div data-page-scroll="true" class="page-scroll">
                    <div class="biz-page-shell">
                        <div class="biz-page-max-width">
                            <a class="biz-page-back-link" href="{{ route('communities') }}">
                                &larr; {{ __('communities.title') ?? 'Communities' }}
                            </a>

                            <div class="biz-page-section" style="margin-top:14px">
                                <div class="zc-com-head">
                                    @if ($community->flag_emoji)
                                        <span class="zc-com-flag">{{ $community->flag_emoji }}</span>
                                    @endif
                                    <h1 class="zc-com-title">{{ $community->name }}</h1>
                                </div>
                                @if ($community->description)
                                    <p class="zc-com-desc">{{ $community->description }}</p>
                                @endif
                                @if ($businessCount > 0)
                                    <div class="zc-com-count">
                                        {{ $businessCount }} {{ $businessCount === 1 ? 'business' : 'businesses' }} accept Zcash in {{ $community->name }}
                                    </div>
                                @endif
                            </div>

                            <div class="biz-page-section">
                                <h2 class="biz-page-section-title">Zcash spots in {{ $community->name }}</h2>

                                @if ($businesses->isNotEmpty())
                                    <div class="zc-biz-grid">
                                        @foreach ($businesses as $b)
                                            <a class="zc-biz-card" href="{{ route('business.show', $b->slug) }}">
                                                @if ($b->logo_url)
                                                    <img class="zc-biz-logo" src="{{ $b->logoDisplayUrl() }}" alt="{{ $b->name }}">
                                                @else
                                                    <span class="zc-biz-logo-ph">{{ \Illuminate\Support\Str::substr($b->name, 0, 1) }}</span>
                                                @endif
                                                <span style="min-width:0">
                                                    <span class="zc-biz-name">
                                                        {{ $b->name }}
                                                        @if ($b->verified)<span class="zc-biz-check">&#10003;</span>@endif
                                                    </span>
                                                    <span class="zc-biz-meta">
                                                        {{ $b->category->name ?? 'Business' }}@if ($b->city) · {{ $b->city }}@endif
                                                    </span>
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>

                                    @if ($businessCount > $businesses->count())
                                        <a class="biz-page-link-pill" style="margin-top:16px;display:inline-flex" href="{{ route('browse.businesses') }}">
                                            View all {{ $businessCount }} on Browse
                                        </a>
                                    @endif
                                @else
                                    <p class="zc-empty">
                                        No businesses here yet. As shops in {{ $community->name }} join ZecMap, they will show up here automatically.
                                    </p>
                                @endif
                            </div>
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
