<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Communities map — {{ config('app.name', 'ZecMap') }}</title>
    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => 'Communities map — ' . config('app.name', 'ZecMap'),
            'description' => 'Zcash communities around the world on ZecMap.',
        ],
    ])
    @include('layouts.partials.app-styles')
    <style>
        .zc-map-head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:16px}
        .zc-map-title{font-size:1.7rem;font-weight:800;margin:0}
        .zc-map-sub{opacity:.7;margin:4px 0 0;font-size:.95rem}
        .zc-map-panel{position:relative;border-radius:20px;overflow:hidden;border:1px solid rgba(140,165,225,.18);
            background:radial-gradient(60% 60% at 20% 12%, rgba(244,183,40,.08), transparent 60%),linear-gradient(150deg,#0b1222,#0a0f1e 60%,#080c18)}
        .zc-map-panel svg{display:block;width:100%;height:auto}
        .zc-land{fill:#141d33;stroke:#243458;stroke-width:.5}
        .zc-grat{fill:none;stroke:#2a3a60;stroke-opacity:.2;stroke-width:.4}
        .zc-pin-hit{cursor:pointer}
        .zc-pin-glow{fill:#F4B728;opacity:.16}
        .zc-pin{fill:#F4B728;stroke:#1a1204;stroke-width:1}
        .zc-pin-lab{fill:#f5f7fc;font-size:10.5px;font-weight:600;text-anchor:middle;paint-order:stroke;stroke:#0a0f1e;stroke-width:3.5px}
        .zc-pin-hit:hover .zc-pin{fill:#ffd24a}
        .zc-pin-hit:hover .zc-pin-glow{opacity:.3}
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
                            <div class="zc-map-head">
                                <div>
                                    <h1 class="zc-map-title">Communities map</h1>
                                    <p class="zc-map-sub">Tap a community to open its page and the Zcash spots in its region.</p>
                                </div>
                                <a class="biz-page-link-pill" href="{{ route('communities') }}">Community leaderboard</a>
                            </div>
                            <div class="zc-map-panel"><svg id="zc-com-map" viewBox="0 0 960 500" role="img" aria-label="World map of Zcash communities"></svg></div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </main>
            @include('layouts.partials.mobile-bottom-nav')
        </div>
        <div class="toast-stack"></div>
    </div>

    <script src="{{ asset('assets/vendor/d3.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/topojson-client.min.js') }}"></script>
    <script>
        (function () {
            var pins = @json($pins);
            var W = 960, H = 500;
            var svg = d3.select('#zc-com-map');
            d3.json(@json(asset('assets/vendor/countries-110m.json'))).then(function (world) {
                var land = topojson.feature(world, world.objects.countries);
                var proj = d3.geoNaturalEarth1().fitExtent([[20, 20], [W - 20, H - 20]], land);
                var path = d3.geoPath(proj);
                svg.append('path').datum(d3.geoGraticule10()).attr('class', 'zc-grat').attr('d', path);
                svg.append('g').selectAll('path').data(land.features).join('path').attr('class', 'zc-land').attr('d', path);

                var g = svg.append('g');
                pins.forEach(function (p) {
                    var xy = proj([p.lng, p.lat]);
                    if (!xy) return;
                    var a = g.append('a').attr('href', p.url).attr('class', 'zc-pin-hit');
                    a.append('circle').attr('cx', xy[0]).attr('cy', xy[1]).attr('r', 13).attr('class', 'zc-pin-glow');
                    a.append('circle').attr('cx', xy[0]).attr('cy', xy[1]).attr('r', 6).attr('class', 'zc-pin');
                    a.append('text').attr('x', xy[0]).attr('y', xy[1] - 11).attr('class', 'zc-pin-lab')
                        .text((p.flag ? p.flag + ' ' : '') + (p.count ? p.count : ''));
                    a.append('title').text(p.name + (p.count ? ' — ' + p.count + ' businesses' : ''));
                });
            });
        })();
    </script>
</body>

</html>
