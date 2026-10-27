@php
    /** @var array<string, mixed> $business */
@endphp
<article class="browse-card" data-browse-card data-name="{{ $business['name'] }}"
    data-category="{{ $business['category_slug'] ?? '' }}"
    data-verified="{{ $business['verified'] ? '1' : '0' }}"
    data-created="{{ $business['created_at'] }}"
    data-search="{{ $business['search_text'] }}">
    <div class="browse-card-cover browse-card-cover--{{ $business['cover_class'] }}">
        @if ($business['logo_url'])
            <img alt="{{ $business['name'] }}" class="browse-card-image" src="{{ $business['logo_url'] }}">
        @else
            <div class="browse-card-cover-icon browse-card-cover-icon--{{ $business['cover_class'] }}" aria-hidden="true">
            </div>
        @endif
        <div class="browse-card-cover-overlay"></div>
        @if (! empty($business['badge']))
            <span
                class="browse-card-badge browse-card-badge--{{ $business['badge']['class'] }}">{{ $business['badge']['label'] }}</span>
        @endif
        @if ($business['verified'])
            <div class="browse-card-verified">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-badge-check browse-card-verified-icon" aria-hidden="true">
                    <path
                        d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                    </path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
        @endif
    </div>
    <div class="browse-card-body">
        <div>
            <a class="browse-card-name" href="{{ $business['detail_url'] }}">{{ $business['name'] }}</a>
            @if ($business['category_name'])
                <span class="browse-card-category" title="{{ $business['category_name'] }}">
                    {{ $business['category_name'] }}
                </span>
            @endif
        </div>
        <div class="browse-card-footer">
            <div class="browse-card-links">
                @if ($business['website'])
                    <a href="{{ $business['website'] }}" target="_blank" rel="noopener noreferrer"
                        class="browse-card-link">
                        <svg class="browse-card-link-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="m23.414,16.09s-8.734-3.97-8.765-3.98c-.728-.248-1.518-.065-2.062.479s-.727,1.334-.442,2.151l3.944,8.675c.159.351.506.579.892.586h.019c.378,0,.725-.214.895-.553l1.851-3.702,3.702-1.851c.345-.173.56-.527.553-.913-.007-.385-.235-.732-.586-.892Zm-4.861,2.016c-.193.097-.351.254-.447.447l-1.064,2.128-3.044-6.676,6.681,3.038-2.125,1.062Zm-5.641,3.854c-.172.015-.345.026-.52.033-.668-.661-2.41-2.518-3.624-4.992h.732c.552,0,1-.447,1-1s-.448-1-1-1h-1.53c-.291-.952-.47-1.96-.47-3s.18-2.047.472-3h8.057c.118.387.218.783.295,1.187.104.543.629.901,1.169.795.542-.103.898-.627.795-1.169-.052-.275-.114-.546-.183-.813h3.393c.013,0,.027,0,.04,0,.299.948.46,1.956.46,3.001,0,.307-.014.61-.041.91-.049.55.356,1.036.906,1.086.552.054,1.037-.356,1.086-.906.032-.359.049-.723.049-1.09C24,5.383,18.617,0,12,0S0,5.383,0,12s5.383,12,11.999,12h0c.367,0,.73-.017,1.088-.049.55-.049.957-.534.908-1.085-.049-.549-.533-.955-1.084-.907Zm-7.02-6.959h-3.392c-.013,0-.026,0-.04,0-.299-.948-.46-1.956-.46-3.001s.161-2.053.46-3.001c.013,0,.026,0,.04,0h3.392c-.245.955-.392,1.959-.392,3s.146,2.045.392,3Zm2.881-8c1.021-2.077,2.415-3.724,3.229-4.588.815.863,2.209,2.507,3.23,4.588h-6.459Zm11.885,0h-3.228c-.791-1.886-1.888-3.488-2.832-4.657,2.579.695,4.749,2.397,6.06,4.657ZM9.4,2.344c-.944,1.169-2.042,2.771-2.832,4.656h-3.226c1.31-2.26,3.479-3.962,6.058-4.656Zm-6.058,14.656h3.226c.79,1.886,1.888,3.487,2.832,4.656-2.578-.695-4.747-2.397-6.058-4.656Z">
                            </path>
                        </svg>Website</a>
                @endif
                @if ($business['twitter_url'])
                    <a href="{{ $business['twitter_url'] }}" target="_blank" rel="noopener noreferrer"
                        class="browse-card-link browse-card-link--icon-only" aria-label="X">
                        <svg class="browse-card-link-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="m18.9,1.153h3.682l-8.042,9.189,9.46,12.506h-7.405l-5.804-7.583-6.634,7.583H.469l8.6-9.831L0,1.153h7.593l5.241,6.931,6.065-6.931Zm-1.293,19.494h2.039L6.482,3.239h-2.19l13.314,17.408Z">
                            </path>
                        </svg>
                    </a>
                @endif
            </div>
            <a class="browse-card-details-button" href="{{ $business['detail_url'] }}">View Details<svg
                    class="browse-card-details-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path
                        d="M23.12,9.91,19.25,6a1,1,0,0,0-1.42,0h0a1,1,0,0,0,0,1.41L21.39,11H1a1,1,0,0,0-1,1H0a1,1,0,0,0,1,1H21.45l-3.62,3.61a1,1,0,0,0,0,1.42h0a1,1,0,0,0,1.42,0l3.87-3.88A3,3,0,0,0,23.12,9.91Z">
                    </path>
                </svg>
            </a>
        </div>
    </div>
</article>
