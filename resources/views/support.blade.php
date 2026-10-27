<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Support</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.support') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('support.subtitle'),
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
                    <div class="support-page browse-page-shell">
                        <div class="support-container support-content">
                            <header class="support-hero">
                                <h1 class="support-title">Support Us</h1>
                                <p class="support-subtitle">ZecMap is a community-maintained project. Your
                                    contributions help grow the privacy-focused economy.</p>
                            </header>
                            <div class="support-grid">
                                <section class="support-card support-card--donate"
                                    aria-labelledby="support-donate-title">
                                    <div class="support-card-header">
                                        <div class="support-card-icon-wrap support-card-icon-wrap--primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-wallet support-card-icon" aria-hidden="true">
                                                <path
                                                    d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1">
                                                </path>
                                                <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4">

                                                </path>
                                            </svg>
                                        </div>
                                        <h2 id="support-donate-title" class="support-card-title">ZEC Donation</h2>
                                    </div>
                                    <p class="support-card-desc">Donations to our shielded address go toward server
                                        costs and development.</p>
                                    <div class="support-address-box" data-donation-address>
                                        {{ $donationAddress }}
                                    </div>
                                    <button type="button" class="support-copy-button" data-copy-donation>Copy Address</button>
                                </section>
                                <section class="support-card" aria-labelledby="support-contribute-title">
                                    <div class="support-card-header">
                                        <div class="support-card-icon-wrap support-card-icon-wrap--secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-shield-check support-card-icon" aria-hidden="true">
                                                <path
                                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                                </path>
                                                <path d="m9 12 2 2 4-4">

                                                </path>
                                            </svg>
                                        </div>
                                        <h2 id="support-contribute-title" class="support-card-title">Volunteer</h2>
                                    </div>
                                    <p class="support-card-desc">Add new businesses, verify existing locations, or
                                        contribute code to help the community.</p>
                                    <div class="support-actions">
                                        <a href="{{ url('/add') }}" class="support-action-link">+
                                            Add</a>
                                        <a href="https://github.com/zecmap" target="_blank" rel="noopener noreferrer"
                                            class="support-action-link">GitHub</a>
                                    </div>
                                </section>
                            </div>
                            <section class="support-supporters" aria-labelledby="support-supporters-title">
                                <header class="support-supporters-header">
                                    <h2 id="support-supporters-title" class="support-supporters-title">Our Supporters
                                    </h2>
                                    <p class="support-supporters-subtitle">Thank you to everyone who helps keep ZecMap
                                        running.</p>
                                </header>
                                <ul class="support-supporters-grid">
                                    @forelse ($supporters as $supporter)
                                        <li class="support-supporter-card">
                                            <div class="support-supporter-main">
                                                @if ($supporter->iconDisplayUrl())
                                                    <img alt="" class="support-supporter-icon" loading="lazy"
                                                        src="{{ $supporter->iconDisplayUrl() }}">
                                                @else
                                                    <div class="support-supporter-icon support-supporter-icon--placeholder"
                                                        aria-hidden="true">🌍</div>
                                                @endif
                                                <div class="support-supporter-info">
                                                    <h3 class="support-supporter-name">{{ $supporter->name }}</h3>
                                                    <p class="support-supporter-desc">{{ $supporter->description }}</p>
                                                </div>
                                            </div>
                                            @if ($supporter->hasLink())
                                                <a href="{{ $supporter->link_url }}" target="_blank" rel="noopener noreferrer"
                                                    class="support-supporter-link">{{ $supporter->link_label }}<svg
                                                    xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-external-link" aria-hidden="true">
                                                    <path d="M15 3h6v6"></path>
                                                    <path d="M10 14 21 3"></path>
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6">
                                                    </path>
                                                </svg>
                                            </a>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="support-supporters-empty">No supporters listed yet.</li>
                                    @endforelse
                                </ul>
                            </section>
                            <section class="support-share" aria-labelledby="support-share-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-share2 lucide-share-2 support-share-icon" aria-hidden="true">
                                    <circle cx="18" cy="5" r="3">

                                    </circle>
                                    <circle cx="6" cy="12" r="3">

                                    </circle>
                                    <circle cx="18" cy="19" r="3">

                                    </circle>
                                    <line x1="8.59" x2="15.42" y1="13.51" y2="17.49">

                                    </line>
                                    <line x1="15.41" x2="8.59" y1="6.51" y2="10.49">

                                    </line>
                                </svg>
                                <h3 id="support-share-title" class="support-share-title">Share Us</h3>
                                <p class="support-share-desc">Share ZecMap on social media to help more people discover
                                    the Zcash economy.</p>
                                <div class="support-share-actions">
                                    <button type="button" class="support-share-button support-share-button--primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-share2 lucide-share-2" aria-hidden="true">
                                            <circle cx="18" cy="5" r="3">

                                            </circle>
                                            <circle cx="6" cy="12" r="3">

                                            </circle>
                                            <circle cx="18" cy="19" r="3">

                                            </circle>
                                            <line x1="8.59" x2="15.42" y1="13.51" y2="17.49">
                                            </line>
                                            <line x1="15.41" x2="8.59" y1="6.51" y2="10.49">
                                            </line>
                                        </svg>Share</button>
                                    <a href="https://twitter.com/intent/tweet?text=Discover%20privacy-friendly%20places%20that%20accept%20Zcash!&amp;url=https%3A%2F%2Fzecmap.com"
                                        target="_blank" rel="noopener noreferrer"
                                        class="support-share-button support-share-button--secondary">𝕏
                                        Twitter</a>
                                    <button type="button"
                                        class="support-share-button support-share-button--secondary"
                                        data-copy-page-link>🔗 Copy
                                        Link</button>
                                </div>
                            </section>
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
    <script src="{{ asset('assets/js/support.js') }}" defer></script>
</body>

</html>
