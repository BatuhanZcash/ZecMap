<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Browse — {{ config('app.name', 'ZecMap') }}</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.browse') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('browse.subtitle'),
        ],
    ])

    @include('layouts.partials.app-styles')
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')

            <main class="app-main">
                <div data-page-scroll="true" class="page-scroll">
                    <div class="browse-page browse-page-shell" data-browse-page
                        data-browse-url="{{ route('browse.businesses') }}"
                        data-browse-total="{{ $totalCount }}"
                        data-browse-has-more="{{ $hasMore ? '1' : '0' }}"
                        data-browse-next-page="{{ $nextPage ?? '' }}">
                        <div class="browse-container">
                            <div class="browse-layout">
                                <aside class="browse-sidebar">
                                    <div class="browse-sidebar-header">
                                        <h1 class="browse-title">Browse Merchants</h1>
                                        <p class="browse-subtitle">Discover merchants accepting private payments
                                            globally.</p>
                                    </div>

                                    <div class="browse-sidebar-block browse-search--desktop">
                                        <h3 class="browse-section-title">Search</h3>
                                        <div class="browse-search">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-search browse-search-icon" aria-hidden="true">
                                                <path d="m21 21-4.34-4.34"></path>
                                                <circle cx="11" cy="11" r="8"></circle>
                                            </svg>
                                            <input placeholder="Search merchants..." class="browse-search-input"
                                                type="search" data-browse-search autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="browse-sidebar-block">
                                        <div class="browse-section-header">
                                            <h3 class="browse-section-title">Categories</h3>
                                        </div>

                                        <div class="browse-mobile-header">
                                            <div>
                                                <h1 class="browse-title">Browse Merchants</h1>
                                                <p class="browse-subtitle">Discover merchants accepting private
                                                    payments globally.</p>
                                            </div>
                                            <div class="browse-search">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-search browse-search-icon" aria-hidden="true">
                                                    <path d="m21 21-4.34-4.34"></path>
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                </svg>
                                                <input placeholder="Search merchants..." class="browse-search-input"
                                                    type="search" data-browse-search autocomplete="off">
                                            </div>

                                            <div class="browse-filter-scroll">
                                                <div class="browse-filter-row">
                                                    <button type="button"
                                                        class="browse-filter-chip browse-filter-chip--active"
                                                        data-browse-chip="">All</button>
                                                    @foreach ($categories as $category)
                                                        <button type="button" class="browse-filter-chip"
                                                            data-browse-chip="{{ $category->slug }}">
                                                            {{ $category->name }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="browse-category-list">
                                            <button type="button"
                                                class="browse-category-button browse-category-button--active"
                                                data-browse-category="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-layout-grid" aria-hidden="true">
                                                    <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                                                    <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                                                    <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                                                    <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                                                </svg>
                                                All Categories
                                            </button>

                                            @foreach ($categories as $category)
                                                <button type="button" class="browse-category-button"
                                                    data-browse-category="{{ $category->slug }}">
                                                    @include('browse.partials.category-icon', [
                                                        'slug' => $category->slug,
                                                    ])
                                                    {{ $category->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="browse-sidebar-block browse-sort-section">
                                        <h3 class="browse-section-title">Sorting</h3>
                                        <div class="browse-sort-list">
                                            <button type="button"
                                                class="browse-sort-button browse-sort-button--active"
                                                data-browse-sort="newest">Newest First</button>
                                            <button type="button" class="browse-sort-button"
                                                data-browse-sort="verified">Most Verified</button>
                                            <button type="button" class="browse-sort-button"
                                                data-browse-sort="az">A – Z</button>
                                            <button type="button" class="browse-sort-button"
                                                data-browse-sort="za">Z – A</button>
                                        </div>
                                    </div>
                                </aside>

                                <div class="browse-main">
                                    <div class="browse-results-bar">
                                        <span class="browse-results-count" id="browse-results-count">
                                            {{ $totalCount }} {{ $totalCount === 1 ? 'Place' : 'Places' }} Found
                                        </span>
                                        <select class="browse-sort-select" id="browse-sort-select">
                                            <option value="newest">Newest First</option>
                                            <option value="verified">Most Verified</option>
                                            <option value="az">A – Z</option>
                                        </select>
                                    </div>

                                    <div class="browse-grid" id="browse-grid" @if ($totalCount === 0) hidden @endif>
                                        @foreach ($businesses as $business)
                                            @include('browse.partials.card', ['business' => $business])
                                        @endforeach
                                    </div>

                                    <div id="browse-load-more-wrap" class="browse-load-more-wrap"
                                        @if (! $hasMore) hidden @endif>
                                        <button type="button" class="browse-load-more-btn" id="browse-load-more-btn"
                                            data-browse-load-more>
                                            Show more
                                        </button>
                                    </div>

                                    <div class="browse-empty" id="browse-empty"
                                        @if ($totalCount > 0) hidden @endif>
                                        <div class="browse-empty-icon-wrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-search-x browse-empty-icon" aria-hidden="true">
                                                <path d="m13.5 13.5-6-6"></path>
                                                <path d="m7.5 13.5 6-6"></path>
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <path d="m21 21-4.3-4.3"></path>
                                            </svg>
                                        </div>
                                        <h2 class="browse-empty-title">No merchants found</h2>
                                        <p class="browse-empty-subtitle">
                                            @if ($totalCount === 0)
                                                There are no active merchants listed yet. Check back soon.
                                            @else
                                                Try adjusting your search or category filter.
                                            @endif
                                        </p>
                                    </div>
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
    <script src="{{ asset('assets/js/browse.js') }}" defer></script>
</body>

</html>
