<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — {{ __('saved.title') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('saved.title') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('saved.subtitle'),
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
                    <div class="profile-page browse-page-shell">
                        <div class="profile-max-width mx-auto w-full px-4 sm:px-6 md:px-10 lg:px-12"
                            style="max-width: 640px; margin: 0 auto; padding: 5rem 1rem 3rem;">

                            <div class="profile-section-header" style="margin-bottom: 1.5rem;">
                                <h1 class="profile-section-title" style="margin-bottom: 0; font-size: 1.125rem; text-transform: none; letter-spacing: 0;">
                                    {{ __('saved.title') }}
                                </h1>
                                <a href="{{ route('profile') }}" class="profile-section-link">{{ __('saved.backToProfile') }}</a>
                            </div>

                            <p class="profile-bio" style="margin-top: 0; margin-bottom: 1.5rem;">
                                {{ __('saved.subtitle') }}
                            </p>

                            @if ($businesses->isEmpty())
                                <div class="profile-reviews-empty">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-bookmark profile-reviews-empty-icon" aria-hidden="true">
                                        <path d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z"></path>
                                    </svg>
                                    <p class="profile-reviews-empty-title">{{ __('saved.emptyTitle') }}</p>
                                    <p class="profile-reviews-empty-text">{{ __('saved.emptyText') }}</p>
                                    <a class="profile-reviews-empty-link" href="{{ route('map') }}">{{ __('saved.exploreMap') }}</a>
                                </div>
                            @else
                                <ul class="profile-business-list" id="saved-business-list">
                                    @foreach ($businesses as $business)
                                        <li class="profile-business-item" data-saved-slug="{{ $business->slug }}">
                                            @if ($business->logoDisplayUrl())
                                                <img alt="" class="profile-business-logo"
                                                    src="{{ $business->logoDisplayUrl() }}">
                                            @else
                                                <span class="profile-business-logo profile-business-logo--placeholder"
                                                    aria-hidden="true">🏪</span>
                                            @endif
                                            <div class="profile-business-info">
                                                <p class="profile-business-name">{{ $business->name }}</p>
                                                <p class="profile-business-category">
                                                    {{ $business->category?->name ?? 'Business' }}
                                                </p>
                                            </div>
                                            <div class="profile-business-actions">
                                                @if ($business->verified)
                                                    <span class="profile-business-verified" title="{{ __('business.verified') }}">✓</span>
                                                @endif
                                                <button type="button"
                                                    class="saved-business-remove"
                                                    data-saved-remove="{{ $business->slug }}"
                                                    aria-label="{{ __('saved.remove', ['name' => $business->name]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-bookmark-minus" aria-hidden="true">
                                                        <path d="M12 10v6"></path>
                                                        <path d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z"></path>
                                                    </svg>
                                                </button>
                                                <a class="profile-business-detail-link"
                                                    href="{{ route('business.show', $business) }}"
                                                    aria-label="{{ __('saved.view', ['name' => $business->name]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-chevron-right" aria-hidden="true">
                                                        <path d="m9 18 6-6-6-6"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
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

    <script>
        window.ZECMAP_FAVORITES = {
            authenticated: @json(auth()->check()),
            slugsUrl: @json(route('saved.slugs')),
            toggleUrlTemplate: @json(route('saved.toggle', ['business' => '__SLUG__'])),
            loginUrl: @json(route('auth')),
            savedText: @json(__('saved.savedToast')),
            removedText: @json(__('saved.removedToast')),
            loginRequiredText: @json(__('saved.loginRequired')),
        };
    </script>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/business-favorites.js') }}" defer></script>
    <script src="{{ asset('assets/js/saved-businesses.js') }}" defer></script>
</body>

</html>
