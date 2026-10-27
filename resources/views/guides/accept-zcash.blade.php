<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Start accepting Zcash — {{ config('app.name', 'ZecMap') }}</title>
    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => 'Start accepting Zcash at your business',
            'description' => 'A step by step guide for merchants: get a wallet, list your business on ZecMap, show a payment QR, print a poster and take private, low fee Zcash payments.',
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
                    @php $base = asset('guides/zero-to-shielded/shots'); @endphp
                    <div class="mg">
                        <div class="mg-wrap">
                            <header class="mg-hero">
                                <p class="mg-kicker">For businesses · 5 steps · about 15 minutes</p>
                                <h1>Start accepting <span class="gold">Zcash</span></h1>
                                <p class="mg-lead">
                                    Take private, low fee payments from anywhere in the world. No bank, no middleman,
                                    no chargebacks. The money goes straight to your own wallet. Here is how, step by step.
                                </p>
                                <div class="mg-cta">
                                    <a class="mg-btn mg-btn--gold" href="{{ url('/add') }}">Add your business</a>
                                    <a class="mg-btn" href="{{ url('/zcash-wallet-guide') }}">Need a wallet first?</a>
                                </div>
                            </header>

                            {{-- Step 1 --}}
                            <section class="mg-step">
                                <div class="mg-shot mg-shot--phone">
                                    <img src="{{ $base }}/noir/1-08-home.jpg" alt="A Zcash wallet home screen" loading="lazy">
                                </div>
                                <div class="mg-text">
                                    <span class="mg-num">01</span>
                                    <h2>Get a Zcash wallet</h2>
                                    <p>Install a Zcash wallet on your phone. Zashi, Zodl, Vizor, Noir and Zingo all work.
                                        If you are new to Zcash, our <a href="{{ url('/zcash-wallet-guide') }}">wallet guide</a>
                                        walks through every screen with real screenshots.</p>
                                </div>
                            </section>

                            {{-- Step 2 --}}
                            <section class="mg-step mg-step--rev">
                                <div class="mg-shot mg-shot--phone">
                                    <img src="{{ $base }}/noir/2-01-receive-shielded.jpg" alt="Copying a shielded receiving address" loading="lazy">
                                </div>
                                <div class="mg-text">
                                    <span class="mg-num">02</span>
                                    <h2>Copy your shielded address</h2>
                                    <p>Open the wallet, tap <b>Receive</b>, and copy your <b>shielded</b> address
                                        (it starts with <code>u1…</code> or <code>zs…</code>). Shielded means the amount
                                        and who paid whom stay private. This is the address you will accept payments on.</p>
                                </div>
                            </section>

                            {{-- Step 3 --}}
                            <section class="mg-step">
                                <div class="mg-shot mg-shot--ui">
                                    <img src="{{ asset('guides/accept-zcash/zm-add-wallet.png') }}" alt="Submitting a business on ZecMap" loading="lazy">
                                </div>
                                <div class="mg-text">
                                    <span class="mg-num">03</span>
                                    <h2>List your business on ZecMap</h2>
                                    <p>Go to <a href="{{ url('/add') }}">Add Listing</a>, fill in your name, category and a
                                        photo, and paste your Zcash address. Once our team approves it, your business gets
                                        its own page on ZecMap and appears on the map.</p>
                                </div>
                            </section>

                            {{-- Step 4 --}}
                            <section class="mg-step mg-step--rev">
                                <div class="mg-shot mg-shot--ui">
                                    <img src="{{ asset('guides/accept-zcash/zm-qr.png') }}" alt="Your ZecMap page with a payment QR and converter" loading="lazy">
                                </div>
                                <div class="mg-text">
                                    <span class="mg-num">04</span>
                                    <h2>Show your QR and print a poster</h2>
                                    <p>Your page builds a payment QR from your address, with a live price converter so you
                                        can turn any amount into ZEC. One click gives you a clean, printable
                                        <b>poster</b> for your counter or window, with your own QR and the Zcash logo.</p>
                                </div>
                            </section>

                            {{-- Step 5 --}}
                            <section class="mg-step">
                                <div class="mg-shot mg-shot--phone">
                                    <img src="{{ $base }}/vizor/2-07-received.jpg" alt="A received Zcash payment in the wallet" loading="lazy">
                                </div>
                                <div class="mg-text">
                                    <span class="mg-num">05</span>
                                    <h2>Get paid, privately</h2>
                                    <p>A customer scans the QR, their wallet opens with the amount already filled in, and
                                        they confirm. The ZEC lands in your wallet in seconds, with a tiny fee. You see it
                                        arrive right there in your own wallet. No bank, no chargeback, no middleman.</p>
                                </div>
                            </section>

                            <section class="mg-end">
                                <h2>Ready to take Zcash?</h2>
                                <p>It takes about 15 minutes and costs nothing to list.</p>
                                <div class="mg-cta">
                                    <a class="mg-btn mg-btn--gold" href="{{ url('/add') }}">Add your business</a>
                                    <a class="mg-btn" href="{{ url('/zcash-wallet-guide') }}">Open the wallet guide</a>
                                </div>
                            </section>
                        </div>
                    </div>

                    @include('layouts.footer')
                </div>
            </main>
            @include('layouts.partials.mobile-bottom-nav')
        </div>
        <div class="toast-stack"></div>
    </div>

    <style>
        .mg { padding: 72px 20px 60px; }
        .mg-wrap { max-width: 980px; margin: 0 auto; }
        .mg .gold { color: #F3B826; }
        .mg-hero { text-align: center; max-width: 720px; margin: 0 auto 20px; }
        .mg-kicker { font-size: 13px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: #9A6A00; }
        html.dark .mg-kicker { color: #F3B826; }
        .mg-hero h1 { font-family: "Poppins","Inter",sans-serif; font-weight: 800; font-size: clamp(34px,6vw,56px);
            line-height: 1.05; letter-spacing: -.02em; margin: 10px 0 0; }
        .mg-lead { font-size: 18px; line-height: 1.6; opacity: .78; margin: 16px auto 0; max-width: 60ch; }
        .mg-cta { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-top: 24px; }
        .mg-btn { display: inline-flex; align-items: center; padding: 12px 22px; border-radius: 999px; font-weight: 700;
            font-size: 15px; text-decoration: none; border: 1px solid rgba(128,128,128,.35); color: inherit; }
        .mg-btn--gold { background: #F3B826; color: #01061C; border-color: #F3B826; }

        .mg-step { display: grid; grid-template-columns: 300px 1fr; gap: 44px; align-items: center; margin-top: 64px; }
        .mg-step--rev .mg-shot { order: 2; }
        .mg-num { font-family: "Poppins",sans-serif; font-weight: 800; font-size: 15px; color: #F3B826; letter-spacing: .08em; }
        .mg-text h2 { font-family: "Poppins","Inter",sans-serif; font-weight: 700; font-size: clamp(22px,3.4vw,30px);
            letter-spacing: -.01em; margin: 8px 0 12px; }
        .mg-text p { font-size: 16.5px; line-height: 1.65; opacity: .82; }
        .mg-text a { color: #9A6A00; font-weight: 600; }
        html.dark .mg-text a { color: #F3B826; }
        .mg-text code { font-family: ui-monospace, Menlo, monospace; font-size: .9em; background: rgba(128,128,128,.14);
            padding: 1px 6px; border-radius: 6px; }

        .mg-shot img { display: block; width: 100%; height: auto; }
        .mg-shot--phone { max-width: 270px; margin: 0 auto; }
        .mg-shot--phone img { border-radius: 26px; border: 1px solid rgba(128,128,128,.22); box-shadow: 0 18px 50px rgba(0,5,28,.18); }
        .mg-shot--ui img { border-radius: 16px; border: 1px solid rgba(128,128,128,.18); box-shadow: 0 16px 44px rgba(0,5,28,.14); }

        .mg-end { text-align: center; margin: 80px auto 0; max-width: 620px; }
        .mg-end h2 { font-family: "Poppins","Inter",sans-serif; font-weight: 800; font-size: clamp(26px,4vw,38px); }
        .mg-end p { opacity: .78; margin-top: 8px; font-size: 17px; }

        @media (max-width: 760px) {
            .mg { padding-top: 56px; }
            .mg-step, .mg-step--rev { grid-template-columns: 1fr; gap: 22px; margin-top: 52px; text-align: center; }
            .mg-step--rev .mg-shot { order: 0; }
            .mg-text { display: flex; flex-direction: column; align-items: center; }
            .mg-shot--ui { max-width: 520px; margin: 0 auto; }
        }
    </style>

    <script src="{{ asset('assets/js/home.js') }}" defer></script>
</body>

</html>
