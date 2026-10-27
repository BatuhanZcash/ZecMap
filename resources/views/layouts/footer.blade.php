<footer aria-label="Site footer" class="footer">
    <div class="footer-main">
        <div class="footer-brand">
            <a class="footer-logo" aria-label="ZecMap" href="{{ url('/') }}">
                <span class="header-logo-icon">
                    <img alt="ZecMap" class="header-logo-icon" src="{{ asset('assets/images/logo-icon.png') }}">
                    <span class="header-logo-glow"></span>
                </span>
                <span class="footer-wordmark">
                    <span class="footer-wordmark-gold">zec</span>
                    <span class="footer-wordmark-text">map</span>
                </span>
            </a>
            <p class="footer-description">{{ __('footer.description') }}</p>
            <p class="footer-map-data">{{ __('footer.mapData') }}</p>
        </div>
        <nav aria-label="Directory links">
            <h2 class="footer-nav-title">{{ __('footer.directory') }}</h2>
            <ul class="footer-nav-list">
                <li>
                    <a class="footer-nav-link" href="{{ url('/map') }}">{{ __('footer.exploreMap') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ url('/browse') }}">{{ __('footer.browseMerchants') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ url('/add') }}">{{ __('footer.submitBusiness') }}</a>
                </li>
            </ul>
        </nav>
        <nav aria-label="Community links">
            <h2 class="footer-nav-title">{{ __('footer.community') }}</h2>
            <ul class="footer-nav-list">
                <li>
                    <a class="footer-nav-link" href="{{ url('/communities') }}">{{ __('footer.communities') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ url('/profile') }}">{{ __('footer.myProfile') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ route('analytics') }}">{{ __('footer.analytics') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ url('/blog') }}">{{ __('nav.blog') }}</a>
                </li>
                <li>
                    <a class="footer-nav-link" href="{{ url('/auth') }}">{{ __('footer.signIn') }}</a>
                </li>
            </ul>
        </nav>
        <nav aria-label="External resources">
            <h2 class="footer-nav-title">{{ __('footer.resources') }}</h2>
            <ul class="footer-nav-list">
                <li>
                    <a href="https://zecmarket.org" target="_blank" rel="noopener noreferrer"
                        class="footer-nav-link">{{ __('footer.zecmarket') }}</a>
                </li>
                <li>
                    <a href="https://z.cash" target="_blank" rel="noopener noreferrer"
                        class="footer-nav-link">{{ __('footer.whatIsZcash') }}</a>
                </li>
                <li>
                    <a href="https://zechub.wiki" target="_blank" rel="noopener noreferrer"
                        class="footer-nav-link">{{ __('footer.learnZcash') }}</a>
                </li>
                <li>
                    <a href="https://www.openstreetmap.org" target="_blank" rel="noopener noreferrer"
                        class="footer-nav-link">{{ __('footer.openStreetMap') }}</a>
                </li>
                <li>
                    <a href="https://github.com/zecmap" target="_blank" rel="noopener noreferrer"
                        class="footer-nav-link">{{ __('footer.github') }}</a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="footer-bottom">
        <span>{{ __('footer.copyright', ['year' => date('Y')]) }}</span>
        <div class="footer-bottom-actions">
            <div class="footer-social">
                <a href="https://twitter.com/zecmap" target="_blank" rel="noopener noreferrer"
                    class="footer-social-link" title="Twitter/X" aria-label="Follow ZecMap on Twitter">
                    <span>𝕏</span>
                </a>
                <a href="https://github.com/zecmap" target="_blank" rel="noopener noreferrer" class="footer-social-link"
                    title="GitHub" aria-label="ZecMap on GitHub">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.1.39-1.99 1.03-2.69-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.69 0 3.84-2.34 4.69-4.57 4.93.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0012 2z">
                        </path>
                    </svg>
                </a>
                <a href="mailto:hello@zecmap.com" class="footer-social-link" title="Email" aria-label="Email ZecMap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-mail" aria-hidden="true">
                        <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    </svg>
                </a>
                <a href="https://t.me/zecmap" target="_blank" rel="noopener noreferrer" class="footer-social-link"
                    title="Telegram" aria-label="ZecMap on Telegram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-send" aria-hidden="true">
                        <path
                            d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z">
                        </path>
                        <path d="m21.854 2.147-10.94 10.939"></path>
                    </svg>
                </a>
            </div>
            <div class="footer-social-divider"></div>
            <div class="footer-legal">
                <a class="footer-legal-link" href="{{ route('privacy') }}">{{ __('footer.privacyPolicy') }}</a>
                <a class="footer-legal-link" href="{{ route('terms') }}">{{ __('footer.termsOfService') }}</a>
            </div>
        </div>
    </div>
</footer>
