<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => config('app.name', 'ZecMap'),
            'description' => __('landing.subtitle'),
        ],
    ])

    @include('layouts.partials.app-styles')
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')

            <main class="app-main">
                <div class="landing-page">
                    <div class="landing-page-bg">
                        <img alt="" class="landing-page-bg-image" aria-hidden="true"
                            src="{{ asset('assets/images/landing-bg.png') }}">
                    </div>
                    <div class="page-scroll" data-page-scroll="true">
                        <div class="landing-shell">
                            <div class="landing-container">
                                <section class="landing-hero">
                                    <div class="landing-hero-content">
                                        <div class="landing-badge">{{ __('landing.badge') }}</div>
                                        <h1 class="landing-title">
                                            {{ __('landing.title1') }}
                                            <span
                                                class="landing-title-highlight">{{ __('landing.titleHighlight') }}</span>
                                            <br>{{ __('landing.title2') }}
                                        </h1>
                                        <p class="landing-subtitle">
                                            {{ __('landing.subtitle') }}
                                        </p>
                                        <a class="header-add-button header-add-button--fit" href="{{ url('/add') }}">
                                            {{ __('landing.addListing') }}
                                            <img alt="" class="header-add-button-icon" aria-hidden="true"
                                                src="{{ asset('assets/images/arrow-up-right.svg') }}">
                                        </a>
                                    </div>
                                    <div class="landing-hero-visual">
                                        <div class="landing-hero-visual-phone">
                                            <img alt="" class="landing-hero-phone" aria-hidden="true"
                                                src="{{ asset('assets/images/landingphone.png') }}">
                                            <img alt="" class="landing-hero-phone-2" aria-hidden="true"
                                                src="{{ asset('assets/images/landingphone.png') }}">
                                        </div>
                                        <div class="landing-hero-visual-actions">
                                            <div class="landing-actions">
                                                <span
                                                    class="landing-actions-title">{{ __('landing.platforms') }}</span>
                                                <div class="landing-actions-inner">
                                                    <div class="landing-platform landing-platform--active">
                                                        <a class="landing-platform-button landing-platform-button--primary"
                                                            href="https://play.google.com/store/apps/details?id=com.zecmap.app"
                                                            target="_blank" rel="noopener noreferrer"
                                                            aria-label="Android — {{ __('common.android') }}"
                                                            title="{{ __('common.android') }}">
                                                            <svg viewBox="0 0 24 24" width="28" height="28"
                                                                fill="currentColor">
                                                                <path
                                                                    d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85-.29-.15-.65-.06-.83.22l-1.88 3.24c-2.86-1.21-6.08-1.21-8.94 0L5.65 5.67c-.19-.28-.56-.38-.85-.22-.29.17-.4.54-.24.85l1.84 3.18C3.4 11.2 1.5 14.36 1.5 18h21c0-3.64-1.9-6.8-4.9-8.52zM7 15.25c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25zm10 0c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <span
                                                            class="landing-platform-label landing-platform-label--active">{{ __('common.android') }}</span>
                                                    </div>
                                                    <div class="landing-platform landing-platform--active">
                                                        <a class="landing-platform-button landing-platform-button--primary"
                                                            href="{{ url('/map') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="28"
                                                                height="28" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-globe" aria-hidden="true">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <path
                                                                    d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20">
                                                                </path>
                                                                <path d="M2 12h20"></path>
                                                            </svg>
                                                        </a>
                                                        <span
                                                            class="landing-platform-label landing-platform-label--active">{{ __('common.web') }}</span>
                                                    </div>
                                                    <div class="landing-platform landing-platform--active">
                                                        <a class="landing-platform-button landing-platform-button--primary"
                                                            href="https://apps.apple.com/tr/app/zecmap/id6780211916"
                                                            target="_blank" rel="noopener noreferrer"
                                                            aria-label="iOS — {{ __('common.ios') }}"
                                                            title="{{ __('common.ios') }}">
                                                            <svg viewBox="0 0 24 24" width="28" height="28"
                                                                fill="currentColor" aria-hidden="true">
                                                                <path
                                                                    d="M18.71 19.5c-.83 1.24-1.71 2.45-3.1 2.48-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.97 12.01 4.75 8.92c.88-1.53 2.37-2.5 4-2.53 1.25-.02 2.43.83 3.2.83s2.21-1.01 3.74-.85c.64.02 2.44.25 3.59 2.87-1.15.69-1.92 1.63-1.92 3.19 0 1.9 1.4 2.8 3.32 3.24-.13.33-.53 1.15-1.03 1.83zM15.17 3.38c.73-.89 1.22-2.13 1.08-3.38-1.08.04-2.39.71-3.16 1.6-.69.8-1.3 1.99-1.14 3.2 1.2.09 2.42-.6 3.22-1.42z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <span
                                                            class="landing-platform-label landing-platform-label--active">{{ __('common.ios') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="landing-market-section">
                                    <div class="landing-market-card">
                                        <div class="landing-market-text">
                                            <span class="landing-market-eyebrow">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <path d="M3 6h18"></path>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                                {{ __('landing.marketEyebrow') }}
                                            </span>
                                            <h2 class="landing-market-title">{{ __('landing.marketTitle') }}</h2>
                                            <p class="landing-market-desc">{{ __('landing.marketDesc') }}</p>
                                        </div>
                                        <div class="landing-market-actions">
                                            <a class="landing-market-button" href="https://zecmarket.org"
                                                target="_blank" rel="noopener noreferrer">
                                                {{ __('landing.marketCta') }}
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M7 17 17 7"></path>
                                                    <path d="M7 7h10v10"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </section>
                                <section class="landing-cta-section">
                                    <div class="landing-cta-card">
                                        <div class="landing-cta-inner">
                                            <div class="landing-cta-text">
                                                <h2 class="landing-cta-title">{{ __('landing.joinNetwork') }}</h2>
                                                <p class="landing-cta-subtitle">
                                                    {{ __('landing.joinSubtitle') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        @include('layouts.footer')
                    </div>
                </div>
            </main>

            @include('layouts.partials.mobile-bottom-nav')
        </div>

        <div class="toast-stack"></div>
    </div>

    <script src="{{ asset('assets/js/home.js') }}" defer></script>
</body>

</html>
