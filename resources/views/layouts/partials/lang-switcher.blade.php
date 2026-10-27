@php
    $currentLocale = $currentLocale ?? app()->getLocale();
    $availableLocales = $availableLocales ?? config('locales.supported', []);
    $current = $availableLocales[$currentLocale] ?? ['code' => strtoupper($currentLocale)];
@endphp

<div class="lang-switcher" data-lang-switcher>
    <button type="button" class="lang-switcher-button" title="{{ __('common.changeLanguage') }}"
        aria-expanded="false" aria-haspopup="listbox" data-lang-switcher-button>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-globe lang-switcher-icon" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
            <path d="M2 12h20"></path>
        </svg>
        <span class="lang-switcher-code">{{ $current['code'] ?? strtoupper($currentLocale) }}</span>
        <img alt="" class="lang-switcher-arrow" aria-hidden="true"
            src="{{ asset('assets/images/angle-down.svg') }}">
    </button>
    <div class="lang-switcher-dropdown" hidden role="listbox" aria-label="{{ __('common.selectLanguage') }}"
        data-lang-switcher-dropdown>
        <div class="lang-switcher-dropdown-header">
            <p class="lang-switcher-dropdown-title">{{ __('common.selectLanguage') }}</p>
        </div>
        @foreach ($availableLocales as $code => $locale)
            <a href="{{ route('locale.switch', $code) }}" role="option"
                @class([
                    'lang-switcher-option',
                    'lang-switcher-option--active' => $currentLocale === $code,
                ])
                @if ($currentLocale === $code) aria-selected="true" @endif>
                {{ $locale['native'] ?? $locale['label'] ?? strtoupper($code) }}
                @if ($currentLocale === $code)
                    <span class="lang-switcher-check" aria-hidden="true">✓</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
