<header class="header">
    <div class="header-shell">
        <div class="header-actions-right header-desktop-only">
            <button type="button" class="header-theme-button" aria-label="{{ __('common.toggleTheme') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-moon" aria-hidden="true">
                    <path
                        d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401">
                    </path>
                </svg>
            </button>
            @include('layouts.partials.lang-switcher')
        </div>
        <div class="header-mobile-bar">
            <a class="header-logo" aria-label="ZecMap" href="{{ url('/') }}">
                <span class="header-logo-icon">
                    <img alt="ZecMap" class="header-logo-icon" src="{{ asset('assets/images/logo-icon.png') }}">
                    <span class="header-logo-glow"></span>
                </span>
                <span class="header-wordmark">
                    <span class="header-wordmark-gold">zec</span>map
                </span>
            </a>
            <div class="header-mobile-actions">
                <button type="button" class="header-theme-button header-mobile-icon-button"
                    aria-label="{{ __('common.toggleTheme') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-moon" aria-hidden="true">
                        <path
                            d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401">
                        </path>
                    </svg>
                </button>
                @auth
                    @include('layouts.partials.header-user-menu')
                @else
                    <a class="header-mobile-login" aria-label="{{ __('common.login') }}" href="{{ route('auth') }}">
                        <img alt="" class="header-add-button-icon" aria-hidden="true"
                            src="{{ asset('assets/images/user.svg') }}">
                    </a>
                @endauth
                <button type="button" class="header-mobile-menu-button" aria-label="{{ __('nav.menu') }}"
                    aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-menu" aria-hidden="true">
                        <path d="M4 5h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 19h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="header-container header-desktop-only">
            <a class="header-logo" aria-label="ZecMap" href="{{ url('/') }}">
                <span class="header-logo-icon">
                    <img alt="ZecMap" class="header-logo-icon" src="{{ asset('assets/images/logo-icon.png') }}">
                    <span class="header-logo-glow"></span>
                </span>
                <span class="header-wordmark">
                    <span class="header-wordmark-gold">zec</span>map
                </span>
            </a>
            <style>
                .header-nav-dd{position:relative;display:inline-flex}
                .header-nav-dd-toggle{display:inline-flex;align-items:center;gap:5px;background:none;border:0;cursor:pointer;font:inherit;color:inherit}
                .header-nav-dd-toggle svg{width:14px;height:14px;transition:transform .18s ease}
                .header-nav-dd:hover .header-nav-dd-toggle svg,.header-nav-dd.open .header-nav-dd-toggle svg{transform:rotate(180deg)}
                .header-nav-dd-menu{position:absolute;top:calc(100% + 12px);left:50%;transform:translateX(-50%) translateY(6px);min-width:196px;background:#0a0f1e;border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:8px;box-shadow:0 20px 52px rgba(0,0,0,.5);opacity:0;visibility:hidden;transition:opacity .16s ease,transform .16s ease;z-index:200}
                .header-nav-dd:hover .header-nav-dd-menu,.header-nav-dd:focus-within .header-nav-dd-menu,.header-nav-dd.open .header-nav-dd-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}
                .header-nav-dd-menu a{display:block;padding:10px 13px;border-radius:9px;color:#f5f7fc;text-decoration:none;font-size:14.5px;font-weight:600;white-space:nowrap}
                .header-nav-dd-menu a:hover{background:rgba(244,183,40,.14);color:#F4B728}
            </style>
            <nav class="header-nav">
                <a class="header-nav-link" href="{{ url('/map') }}">{{ __('nav.explore') }}</a>
                <a class="header-nav-link" href="{{ url('/browse') }}">{{ __('nav.browse') }}</a>
                <a class="header-nav-link" href="{{ url('/accept-zec') }}">{{ __('nav.acceptZec') }}</a>
                <div class="header-nav-dd">
                    <button type="button" class="header-nav-link header-nav-dd-toggle" aria-haspopup="true" aria-expanded="false">
                        {{ __('nav.more') != 'nav.more' ? __('nav.more') : 'More' }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    <div class="header-nav-dd-menu" role="menu">
                        <a href="{{ url('/communities') }}" role="menuitem">{{ __('nav.communities') }}</a>
                        <a href="{{ url('/adoption') }}" role="menuitem">Adoption</a>
                        <a href="{{ url('/start-accepting-zcash') }}" role="menuitem">For merchants</a>
                        <a href="{{ url('/blog') }}" role="menuitem">{{ __('nav.blog') }}</a>
                    </div>
                </div>
                <a class="header-nav-link header-nav-link--market" href="{{ url('/zcash-wallet-guide') }}">Wallet Guide<span class="header-nav-badge">{{ __('common.new') }}</span></a>
                <a class="header-nav-link header-nav-link--market" href="https://zecmarket.org"
                    target="_blank" rel="noopener noreferrer">{{ __('nav.market') }}<span class="header-nav-badge">{{ __('common.new') }}</span></a>
                <a class="header-nav-link header-nav-link--plain"
                    href="{{ url('/support') }}">{{ __('nav.support') }}</a>
            </nav>
            <script>
                (function () {
                    var d = document.querySelector('.header-nav-dd'); if (!d) return;
                    var b = d.querySelector('.header-nav-dd-toggle');
                    b.addEventListener('click', function (e) { e.preventDefault(); var o = d.classList.toggle('open'); b.setAttribute('aria-expanded', o ? 'true' : 'false'); });
                    document.addEventListener('click', function (e) { if (!d.contains(e.target)) { d.classList.remove('open'); b.setAttribute('aria-expanded', 'false'); } });
                })();
            </script>
            <div class="header-actions">
                <a class="header-add-button" href="{{ url('/add') }}">
                    {{ __('nav.addListing') }}
                    <img alt="" class="header-add-button-icon" aria-hidden="true"
                        src="{{ asset('assets/images/plus-small.svg') }}">
                </a>
                @auth
                    @include('layouts.partials.header-user-menu')
                @else
                    <a class="header-login-button" href="{{ route('auth') }}">
                        <img alt="" class="header-add-button-icon" aria-hidden="true"
                            src="{{ asset('assets/images/user.svg') }}">
                        {{ __('common.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
    <div class="header-drawer" aria-hidden="true">
        <button type="button" class="header-drawer-backdrop" aria-label="{{ __('common.close') }}"></button>
        <aside class="header-drawer-panel" role="dialog" aria-modal="true" aria-label="{{ __('nav.menu') }}">
            <div class="header-drawer-head">
                <p class="header-drawer-title">{{ __('nav.menu') }}</p>
                <button type="button" class="header-drawer-close" aria-label="{{ __('common.close') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="header-drawer-nav">
                <a class="header-drawer-link" href="{{ url('/map') }}">{{ __('nav.explore') }}</a>
                <a class="header-drawer-link" href="{{ url('/browse') }}">{{ __('nav.browse') }}</a>
                <a class="header-drawer-link" href="{{ url('/accept-zec') }}">{{ __('nav.acceptZec') }}</a>
                <a class="header-drawer-link" href="{{ url('/communities') }}">{{ __('nav.communities') }}</a>
                <a class="header-drawer-link" href="{{ url('/adoption') }}">Adoption</a>
                <a class="header-drawer-link" href="{{ url('/blog') }}">{{ __('nav.blog') }}</a>
                <a class="header-drawer-link header-drawer-link--market" href="{{ url('/zcash-wallet-guide') }}">Wallet Guide<span class="header-nav-badge">{{ __('common.new') }}</span></a>
                <a class="header-drawer-link header-drawer-link--market" href="https://zecmarket.org"
                    target="_blank" rel="noopener noreferrer">{{ __('nav.market') }}<span class="header-nav-badge">{{ __('common.new') }}</span></a>
                <a class="header-drawer-link" href="{{ url('/support') }}">{{ __('nav.support') }}</a>
            </nav>
            <div class="header-drawer-divider"></div>
            @auth
                <a class="header-drawer-link" href="{{ url('/profile') }}">{{ __('nav.profile') }}</a>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="header-drawer-link header-drawer-signout">{{ __('common.signOut') }}</button>
                </form>
                <div class="header-drawer-divider"></div>
            @else
                <a class="header-drawer-link" href="{{ route('auth') }}">{{ __('common.login') }}</a>
                <div class="header-drawer-divider"></div>
            @endauth
            <a class="header-drawer-add" href="{{ url('/add') }}">
                {{ __('nav.addListing') }}
                <img alt="" class="header-add-button-icon" aria-hidden="true"
                    src="{{ asset('assets/images/plus-small.svg') }}">
            </a>
            <div class="header-drawer-lang">
                @include('layouts.partials.lang-switcher')
            </div>
        </aside>
    </div>
</header>
