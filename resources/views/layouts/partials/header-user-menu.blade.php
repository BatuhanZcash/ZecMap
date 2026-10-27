@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $username = $user->username;
    $initial = $user->initials();
@endphp

<div class="header-user-menu">
    <button type="button" class="header-account-button" aria-haspopup="true"
        aria-label="{{ $username }}">
        <span class="header-account-avatar" aria-hidden="true">
            <span class="header-account-initial">{{ $initial }}</span>
        </span>
        <span class="header-account-name">{{ $username }}</span>
        <img alt="" class="header-account-chevron" aria-hidden="true"
            src="{{ asset('assets/images/angle-small-down.svg') }}">
    </button>
    <div class="header-user-dropdown" role="menu">
        <div class="header-user-dropdown-header">
            <p class="header-user-name">{{ $username }}</p>
            <p class="header-user-handle">{{ '@'.$username }}</p>
        </div>
        <a class="header-dropdown-link" role="menuitem" href="{{ url('/profile') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user" aria-hidden="true">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            {{ __('nav.profile') }}
        </a>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="header-dropdown-signout" role="menuitem">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-log-out" aria-hidden="true">
                    <path d="m16 17 5-5-5-5"></path>
                    <path d="M21 12H9"></path>
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                </svg>
                {{ __('common.signOut') }}
            </button>
        </form>
    </div>
</div>
