@php
    /** @var \App\Models\AcceptZecSite $site */
@endphp
<article class="browse-card accept-zec-card">
    <div class="accept-zec-card-cover">
        @if ($site->logoDisplayUrl())
            <img alt="{{ $site->name }}" class="accept-zec-card-logo" loading="lazy"
                src="{{ $site->logoDisplayUrl() }}">
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                class="accept-zec-card-fallback-icon" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                <path d="M2 12h20"></path>
            </svg>
        @endif
    </div>
    <div class="accept-zec-card-body">
        <h2 class="accept-zec-card-name">{{ $site->name }}</h2>
        <p class="accept-zec-card-desc">{{ $site->description }}</p>
        <a href="{{ $site->website_url }}" target="_blank" rel="noopener noreferrer"
            class="accept-zec-visit-button">
            Visit Site
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-external-link" aria-hidden="true">
                <path d="M15 3h6v6"></path>
                <path d="M10 14 21 3"></path>
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            </svg>
        </a>
    </div>
</article>
