@php
    $activeNav = match (true) {
        request()->is('/') => 'home',
        request()->is('map') => 'map',
        request()->is('browse') => 'browse',
        request()->is('support') => 'support',
        request()->is('profile') => 'profile',
        default => null,
    };
@endphp

<nav class="mobile-bottom-nav" aria-label="{{ __('nav.mobileNav') }}">
    <a @class(['mobile-bottom-nav__link', 'mobile-bottom-nav__link--active' => $activeNav === 'home'])
        href="{{ url('/') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="{{ $activeNav === 'home' ? '2.5' : '2' }}" stroke-linecap="round"
            stroke-linejoin="round" class="lucide lucide-zap mobile-bottom-nav__icon" aria-hidden="true">
            <path
                d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z">
            </path>
        </svg>
        <span class="mobile-bottom-nav__label">{{ __('nav.home') }}</span>
    </a>
    <a @class(['mobile-bottom-nav__link', 'mobile-bottom-nav__link--active' => $activeNav === 'map'])
        href="{{ route('map') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="{{ $activeNav === 'map' ? '2.5' : '2' }}" stroke-linecap="round"
            stroke-linejoin="round" class="lucide lucide-map mobile-bottom-nav__icon" aria-hidden="true">
            <path
                d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z">
            </path>
            <path d="M15 5.764v15"></path>
            <path d="M9 3.236v15"></path>
        </svg>
        <span class="mobile-bottom-nav__label">{{ __('nav.map') }}</span>
    </a>
    <a @class(['mobile-bottom-nav__link', 'mobile-bottom-nav__link--active' => $activeNav === 'browse'])
        href="{{ route('browse') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="{{ $activeNav === 'browse' ? '2.5' : '2' }}" stroke-linecap="round"
            stroke-linejoin="round" class="lucide lucide-grid-3x3 mobile-bottom-nav__icon" aria-hidden="true">
            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
            <path d="M3 9h18"></path>
            <path d="M3 15h18"></path>
            <path d="M9 3v18"></path>
            <path d="M15 3v18"></path>
        </svg>
        <span class="mobile-bottom-nav__label">{{ __('nav.browse') }}</span>
    </a>
    <a @class(['mobile-bottom-nav__link', 'mobile-bottom-nav__link--active' => $activeNav === 'support'])
        href="{{ url('/support') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="{{ $activeNav === 'support' ? '2.5' : '2' }}" stroke-linecap="round"
            stroke-linejoin="round" class="lucide lucide-heart mobile-bottom-nav__icon" aria-hidden="true">
            <path
                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5">
            </path>
        </svg>
        <span class="mobile-bottom-nav__label">{{ __('nav.support') }}</span>
    </a>
    <a @class(['mobile-bottom-nav__link', 'mobile-bottom-nav__link--active' => $activeNav === 'profile'])
        href="{{ url('/profile') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="{{ $activeNav === 'profile' ? '2.5' : '2' }}" stroke-linecap="round"
            stroke-linejoin="round" class="lucide lucide-user mobile-bottom-nav__icon" aria-hidden="true">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span class="mobile-bottom-nav__label">{{ __('nav.profile') }}</span>
    </a>
</nav>
