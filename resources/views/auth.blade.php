<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Login</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('common.signIn') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('seo.login'),
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
                    <div class="auth-page browse-page-shell">
                        <div class="auth-container">
                            <div class="auth-card">
                                <div class="auth-header">
                                    <h1 class="auth-title">{{ __('auth.welcomeBack') }}</h1>
                                    <p class="auth-subtitle">{{ __('auth.loginSubtitle') }}</p>
                                </div>
                                <form class="auth-form" method="POST" action="{{ route('auth.store') }}">
                                    @csrf
                                    @if ($errors->any())
                                        <div class="auth-error" role="alert">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                                    <div class="auth-field">
                                        <label class="auth-label" for="auth-username">{{ __('auth.username') }}</label>
                                        <input id="auth-username" name="username" placeholder="satoshi_123"
                                            required class="auth-input" autocomplete="username" type="text"
                                            value="{{ old('username') }}">
                                    </div>
                                    <div class="auth-field">
                                        <label class="auth-label" for="auth-password">{{ __('auth.password') }}</label>
                                        <input id="auth-password" name="password" placeholder="••••••••"
                                            required minlength="6" class="auth-input"
                                            autocomplete="current-password" type="password">
                                    </div>
                                    <button type="submit" class="auth-submit-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in"
                                            aria-hidden="true">
                                            <path d="m10 17 5-5-5-5"></path>
                                            <path d="M15 12H3"></path>
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                        </svg>
                                        {{ __('common.login') }}
                                    </button>
                                </form>
                                <div class="auth-footer">
                                    {{ __('auth.noAccount') }}
                                    <a href="{{ route('register') }}" class="auth-toggle-button">{{ __('auth.signUp') }}</a>
                                </div>
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
