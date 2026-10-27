<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Register</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => 'Register — ' . config('app.name', 'ZecMap'),
            'description' => __('seo.register'),
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
                                    <h1 class="auth-title">{{ __('auth.joinZecMap') }}</h1>
                                    <p class="auth-subtitle">{{ __('auth.signupSubtitle') }}</p>
                                </div>
                                <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                                    @csrf
                                    @if ($errors->any())
                                        <div class="auth-error" role="alert">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                                    <div class="auth-field">
                                        <label class="auth-label" for="register-username">{{ __('auth.username') }}</label>
                                        <input id="register-username" name="username" placeholder="satoshi_123"
                                            required class="auth-input" autocomplete="username" type="text"
                                            value="{{ old('username') }}">
                                    </div>
                                    <div class="auth-field">
                                        <label class="auth-label" for="register-password">{{ __('auth.password') }}</label>
                                        <input id="register-password" name="password" placeholder="••••••••"
                                            required minlength="6" class="auth-input"
                                            autocomplete="new-password" type="password">
                                    </div>
                                    <button type="submit" class="auth-submit-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"
                                            aria-hidden="true">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <line x1="19" x2="19" y1="8" y2="14"></line>
                                            <line x1="22" x2="16" y1="11" y2="11"></line>
                                        </svg>
                                        {{ __('auth.createAccount') }}
                                    </button>
                                </form>
                                <div class="auth-footer">
                                    {{ __('auth.hasAccount') }}
                                    <a href="{{ route('auth') }}" class="auth-toggle-button">{{ __('auth.login') }}</a>
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
