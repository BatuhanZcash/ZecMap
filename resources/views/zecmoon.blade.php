<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — ZecMoon</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => isset($shareSeq) && $shareSeq
            ? [
                'title' => __('zecmoon.cardTitle', ['n' => $shareSeq]),
                'description' => __('zecmoon.cardDesc', ['n' => $shareSeq]),
                'image' => route('zecmoon.card', ['seq' => $shareSeq]),
            ]
            : [
                'title' => config('app.name', 'ZecMap') . ' — ZecMoon',
                'description' => __('zecmoon.seo'),
                'image' => 'assets/images/zecmoon-og.png',
            ],
    ])

    @include('layouts.partials.app-styles')
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
    <style>
        :root {
            --zmn-panel-bg: rgba(255, 255, 255, 0.92);
            --zmn-panel-border: rgba(0, 5, 28, 0.1);
            --zmn-text: #00051c;
            --zmn-muted: #5f626f;
            --zmn-card-bg: #ffffff;
            --zmn-row-border: rgba(0, 5, 28, 0.06);
            --zmn-row-podium: rgba(243, 184, 38, 0.08);
        }

        html.dark {
            --zmn-panel-bg: rgba(7, 16, 49, 0.92);
            --zmn-panel-border: rgba(255, 255, 255, 0.1);
            --zmn-text: #ffffff;
            --zmn-muted: #cccdd1;
            --zmn-card-bg: #070d26;
            --zmn-row-border: rgba(255, 255, 255, 0.05);
            --zmn-row-podium: rgba(243, 184, 38, 0.07);
        }

        .zmn-view { position: fixed; inset: 0; z-index: 0; }
        .zmn-map { position: absolute; inset: 0; }

        .zmn-hud {
            position: absolute; top: 160px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 10px; z-index: 60; flex-wrap: wrap; justify-content: center;
            padding: 0 12px; width: max-content; max-width: calc(100vw - 16px);
        }
        .zmn-pill {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--zmn-panel-bg); border: 1px solid var(--zmn-panel-border);
            backdrop-filter: blur(8px); color: var(--zmn-text);
            border-radius: 999px; padding: 9px 16px; font-size: 13px; font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 5, 28, 0.18);
        }
        .zmn-pill b { color: #f3b826; font-weight: 700; }
        .zmn-pill .zmn-dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 8px #22c55e; }
        .zmn-pill .zmn-dot--idle { background: var(--zmn-muted); box-shadow: none; }
        .zmn-pill--btn { cursor: pointer; user-select: none; border: 1px solid rgba(243, 184, 38, 0.45); }
        .zmn-pill--btn:hover { background: rgba(243, 184, 38, 0.12); }
        button.zmn-pill { font-family: inherit; }
        .zmn-sound-btn { padding: 9px 12px; }
        .zmn-gate { position: absolute; inset: 0; z-index: 45; display: flex; align-items: center; justify-content: center; background: rgba(0, 5, 28, 0.45); backdrop-filter: blur(2px); padding: 20px; }
        .zmn-gate-card { background: var(--zmn-card-bg); border: 1px solid rgba(243, 184, 38, 0.35); border-radius: 18px; padding: 34px 38px; text-align: center; color: var(--zmn-text); max-width: 380px; box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5); }
        .zmn-gate-card h2 { font-size: 20px; font-weight: 700; margin: 14px 0 8px; }
        .zmn-gate-card p { font-size: 13.5px; color: var(--zmn-muted); line-height: 1.55; margin-bottom: 6px; }
        .zmn-pill--muted { opacity: 0.55; border-color: var(--zmn-panel-border); }

        /* ── Moon marker ── */
        .zmn-moon { cursor: pointer; width: 64px; height: 64px; position: relative; }
        .zmn-moon svg { position: absolute; inset: 0; overflow: visible; }
        @keyframes zmnPulse { 0% { transform: scale(0.55); opacity: 0.85; } 100% { transform: scale(2.1); opacity: 0; } }
        @keyframes zmnFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        .zmn-moon .ring {
            position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px;
            border-radius: 50%; border: 2px solid rgba(61, 255, 158, 0.55);
            animation: zmnPulse 2.2s ease-out infinite; pointer-events: none;
        }
        .zmn-moon .ring--late { animation-delay: 1.1s; }
        .zmn-moon .coin { animation: zmnFloat 3s ease-in-out infinite; filter: drop-shadow(0 0 14px rgba(61, 255, 158, 0.45)) drop-shadow(0 6px 14px rgba(244, 183, 40, 0.4)); }

        /* ── Modals ── */
        .zmn-overlay {
            position: fixed; inset: 0; z-index: 60; display: none;
            align-items: center; justify-content: center;
            background: rgba(0, 5, 28, 0.65); backdrop-filter: blur(4px); padding: 20px;
        }
        .zmn-overlay.open { display: flex; }
        .zmn-modal {
            width: 100%; max-width: 400px; background: var(--zmn-card-bg);
            border: 1px solid rgba(243, 184, 38, 0.35); border-radius: 18px;
            padding: 30px 28px; text-align: center; color: var(--zmn-text);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
        }
        .zmn-modal h3 { font-size: 19px; font-weight: 700; margin: 14px 0 6px; }
        .zmn-modal p { font-size: 13.5px; color: var(--zmn-muted); line-height: 1.55; }
        .zmn-math {
            margin: 18px auto 6px; background: rgba(243, 184, 38, 0.08);
            border: 1px solid var(--zmn-panel-border); border-radius: 12px;
            padding: 16px; font-size: 26px; font-weight: 700; letter-spacing: 0.04em;
        }
        .zmn-math span { color: #f3b826; }
        .zmn-answer {
            margin-top: 12px; width: 130px; background: transparent;
            border: 2px solid #f3b826; border-radius: 10px; padding: 10px;
            font-size: 22px; text-align: center; color: var(--zmn-text);
            font-weight: 700; font-family: inherit; outline: none;
        }
        .zmn-timer { height: 6px; background: rgba(127, 127, 127, 0.2); border-radius: 99px; margin-top: 18px; overflow: hidden; }
        .zmn-timer i { display: block; height: 100%; width: 100%; background: linear-gradient(90deg, #f3b826, #f3cc83); border-radius: 99px; transition: width 0.25s linear; }
        .zmn-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            background: linear-gradient(90deg, #f3b826 0%, #f3cc83 100%); color: #01061c;
            font-weight: 600; font-size: 15px; padding: 12px 24px; border-radius: 8px;
            border: none; cursor: pointer; margin-top: 16px; font-family: inherit; width: 100%;
        }
        .zmn-btn--x { background: #000; color: #fff; border: 1px solid rgba(255, 255, 255, 0.25); text-decoration: none; }
        .zmn-btn--ghost { background: transparent; color: var(--zmn-muted); border: 1px solid var(--zmn-panel-border); }
        .zmn-seq { font-size: 46px; font-weight: 800; color: #f3b826; line-height: 1.1; margin-top: 4px; }
        .zmn-close { position: absolute; top: 14px; right: 16px; background: none; border: none; color: var(--zmn-muted); font-size: 20px; cursor: pointer; }
        .zmn-modal { position: relative; }

        /* ── Leaderboard drawer ── */
        .zmn-lb {
            position: fixed; top: 0; right: 0; bottom: 0; z-index: 55;
            width: min(430px, 100vw); background: var(--zmn-card-bg);
            border-left: 1px solid var(--zmn-panel-border); color: var(--zmn-text);
            transform: translateX(100%); transition: transform 0.28s ease;
            display: flex; flex-direction: column; box-shadow: -20px 0 60px rgba(0, 0, 0, 0.35);
        }
        .zmn-lb.open { transform: translateX(0); }
        .zmn-lb-head { padding: 22px 24px 16px; border-bottom: 1px solid var(--zmn-row-border); position: relative; }
        .zmn-lb-close { position: absolute; top: 18px; right: 18px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: rgba(127, 127, 127, 0.12); border: none; border-radius: 50%; color: var(--zmn-muted); cursor: pointer; }
        .zmn-lb-close:hover { color: var(--zmn-text); background: rgba(127, 127, 127, 0.2); }
        .zmn-lb-head h3 { padding-right: 40px; }
        .zmn-lb-head h3 { font-size: 17px; font-weight: 700; }
        .zmn-lb-head .sub { font-size: 11.5px; color: var(--zmn-muted); margin-top: 2px; }
        .zmn-tabs { display: flex; background: rgba(127, 127, 127, 0.12); border-radius: 9px; padding: 3px; gap: 2px; margin-top: 14px; }
        .zmn-tab { flex: 1; text-align: center; font-size: 12.5px; font-weight: 600; padding: 7px 0; border-radius: 7px; color: var(--zmn-muted); cursor: pointer; }
        .zmn-tab.on { background: #f3b826; color: #01061c; }
        .zmn-lb-body { flex: 1; overflow-y: auto; }
        .zmn-row { display: flex; align-items: center; gap: 12px; padding: 13px 22px; border-bottom: 1px solid var(--zmn-row-border); }
        .zmn-row.podium { background: var(--zmn-row-podium); }
        .zmn-rank { width: 26px; text-align: center; font-weight: 700; font-size: 14px; color: var(--zmn-muted); flex-shrink: 0; }
        .zmn-row.podium .zmn-rank { color: #f3b826; }
        .zmn-av { width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg, #ffe9a8, #f3b826); color: #01061c; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; overflow: hidden; }
        .zmn-av img { width: 100%; height: 100%; object-fit: cover; }
        .zmn-row-user { flex: 1; min-width: 0; font-size: 13.5px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .zmn-row-caps { font-size: 13px; color: var(--zmn-muted); flex-shrink: 0; }
        .zmn-row-caps b { color: #f3b826; font-weight: 700; font-size: 14px; }
        .zmn-prize { font-size: 12.5px; font-weight: 700; color: #22c55e; width: 64px; text-align: right; flex-shrink: 0; }
        .zmn-lb-foot { padding: 14px 22px; border-top: 1px solid var(--zmn-row-border); font-size: 11.5px; color: var(--zmn-muted); }
        .zmn-lb-foot b { color: var(--zmn-text); }
        .zmn-empty { padding: 40px 20px; text-align: center; font-size: 13px; color: var(--zmn-muted); }

        @media (max-width: 640px) {
            .zmn-hud { top: 112px; gap: 6px; }
            .zmn-pill { font-size: 12px; padding: 8px 12px; }
        }
    </style>
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')

            <main class="app-main">
                <div class="zmn-view">
                    <div id="zecmoon-map" class="zmn-map"></div>

                    @if (!empty($isGuest))
                        <div class="zmn-gate">
                            <div class="zmn-gate-card">
                                <svg width="72" height="72" viewBox="0 0 120 120" aria-hidden="true">
                                    <defs><radialGradient id="zmgate" cx="38%" cy="30%"><stop offset="0" stop-color="#ffd463"/><stop offset=".55" stop-color="#f4b728"/><stop offset="1" stop-color="#d99a12"/></radialGradient></defs>
                                    <circle cx="60" cy="60" r="44" fill="none" stroke="#3dff9e" stroke-width="1.4" stroke-dasharray="2 6" opacity=".7"/>
                                    <circle cx="60" cy="60" r="34" fill="url(#zmgate)"/>
                                    <g transform="translate(60,60) scale(0.69) translate(-50,-50)">
                                        <path fill="#ffffff" d="M30 30 L46 30 L46 22 L54 22 L54 30 L70 30 L70 38 L48.5 62 L70 62 L70 70 L54 70 L54 78 L46 78 L46 70 L30 70 L30 62 L51.5 38 L30 38 Z"/>
                                    </g>
                                </svg>
                                <h2>{{ __('zecmoon.gateTitle') }}</h2>
                                <p>{{ __('zecmoon.gateText') }}</p>
                                <a class="zmn-btn" style="width:auto;padding:12px 34px" href="{{ route('auth') }}">{{ __('common.signIn') }}</a>
                            </div>
                        </div>
                    @endif
                    <div class="zmn-hud" @if(!empty($isGuest)) style="display:none" @endif>
                        <span class="zmn-pill" id="zmn-status">
                            <span class="zmn-dot zmn-dot--idle" id="zmn-status-dot"></span>
                            <span id="zmn-status-text">{{ __('zecmoon.scanning') }}</span>
                        </span>
                        <span class="zmn-pill">{{ __('zecmoon.weeklyCaptured') }}: <b id="zmn-week-count">–</b></span>
                        <span class="zmn-pill">{{ __('zecmoon.nextDrop') }}: <b>???</b></span>
                        <span class="zmn-pill zmn-pill--btn" id="zmn-lb-open">{{ __('zecmoon.leaderboard') }}</span>
                        <button type="button" class="zmn-pill zmn-pill--btn zmn-sound-btn" id="zmn-sound"
                            aria-pressed="true" title="{{ __('zecmoon.soundToggle') }}">
                            <svg class="zmn-sound-on" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"></path>
                                <path d="M16 9a5 5 0 0 1 0 6"></path><path d="M19.364 18.364a9 9 0 0 0 0-12.728"></path>
                            </svg>
                            <svg class="zmn-sound-off" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none">
                                <path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"></path>
                                <line x1="22" x2="16" y1="9" y2="15"></line><line x1="16" x2="22" y1="9" y2="15"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- Yakalama modali -->
        <div class="zmn-overlay" id="zmn-catch">
            <div class="zmn-modal">
                <button type="button" class="zmn-close" data-zmn-close>&times;</button>
                <div id="zmn-moon-mini"></div>
                <h3>{{ __('zecmoon.foundTitle') }}</h3>
                <p>{{ __('zecmoon.foundText') }}</p>
                <div class="zmn-math"><span id="zmn-a">?</span> + <span id="zmn-b">?</span> = <span>?</span></div>
                <input type="number" inputmode="numeric" class="zmn-answer" id="zmn-answer" autocomplete="off">
                <div class="zmn-timer"><i id="zmn-timer-bar"></i></div>
                <button type="button" class="zmn-btn" id="zmn-submit">{{ __('zecmoon.submit') }}</button>
            </div>
        </div>

        <!-- Tebrik modali -->
        <div class="zmn-overlay" id="zmn-win">
            <div class="zmn-modal">
                <button type="button" class="zmn-close" data-zmn-close>&times;</button>
                <div id="zmn-moon-win"></div>
                <h3>{{ __('zecmoon.winTitle') }}</h3>
                <div class="zmn-seq">#<span id="zmn-seq">0</span></div>
                <p id="zmn-win-text"></p>
                <a class="zmn-btn zmn-btn--x" id="zmn-share" href="#" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    {{ __('zecmoon.shareOnX') }}
                </a>
            </div>
        </div>

        <!-- Kaçırdın modali -->
        <div class="zmn-overlay" id="zmn-miss">
            <div class="zmn-modal">
                <button type="button" class="zmn-close" data-zmn-close>&times;</button>
                <h3>{{ __('zecmoon.missTitle') }}</h3>
                <p id="zmn-miss-text"></p>
                <button type="button" class="zmn-btn zmn-btn--ghost" data-zmn-close>{{ __('common.close') }}</button>
            </div>
        </div>

        <!-- Liderlik çekmecesi -->
        <aside class="zmn-lb" id="zmn-lb">
            <div class="zmn-lb-head">
                <button type="button" class="zmn-lb-close" id="zmn-lb-close" aria-label="{{ __('common.close') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
                    </svg>
                </button>
                <h3>{{ __('zecmoon.leaderboardTitle') }}</h3>
                <div class="sub">{{ __('zecmoon.leaderboardSub') }}</div>
                <div class="zmn-tabs">
                    <div class="zmn-tab on" data-tab="week">{{ __('zecmoon.thisWeek') }}</div>
                    <div class="zmn-tab" data-tab="all">{{ __('zecmoon.allTime') }}</div>
                </div>
            </div>
            <div class="zmn-lb-body" id="zmn-lb-body">
                <div class="zmn-empty">{{ __('common.loading') }}</div>
            </div>
            <div class="zmn-lb-foot" id="zmn-lb-foot"></div>
        </aside>

        <div class="toast-stack"></div>
    </div>

    <script>
        window.ZECMOON = {
            styleUrlLight: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            styleUrlDark: 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
            pins: @json($mapPins),
            stateUrl: @json(route('zecmoon.state')),
            attemptUrl: @json(route('zecmoon.attempt')),
            captureUrl: @json(route('zecmoon.capture')),
            leaderboardUrl: @json(route('zecmoon.leaderboard')),
            csrf: @json(csrf_token()),
            guest: @json($isGuest ?? false),
            username: @json(auth()->check() ? (auth()->user()->username ?? auth()->user()->name) : null),
            shareUrl: 'https://zecmap.com/zecmoon',
            i18n: {
                active: @json(__('zecmoon.activeNow')),
                activeCount: @json(__('zecmoon.activeCount')),
                scanning: @json(__('zecmoon.scanning')),
                winText: @json(__('zecmoon.winText')),
                missTaken: @json(__('zecmoon.missTaken')),
                missTimeout: @json(__('zecmoon.missTimeout')),
                missWrong: @json(__('zecmoon.missWrong')),
                missToofast: @json(__('zecmoon.missToofast')),
                missLimit: @json(__('zecmoon.missLimit')),
                shareText: @json(__('zecmoon.shareText')),
                prize: @json(__('zecmoon.prize')),
                captures: @json(__('zecmoon.captures')),
                resetsIn: @json(__('zecmoon.resetsIn')),
                rules: @json(__('zecmoon.rules')),
                empty: @json(__('zecmoon.empty')),
            },
        };
    </script>
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ versioned_asset('assets/js/zecmoon.js') }}" defer></script>
</body>

</html>
