<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Add Listing</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.addListing') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('seo.add_listing'),
        ],
    ])

    @include('layouts.partials.app-styles')
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
</head>

<body>
    <div id="root">
        <div class="app-shell">
            @include('layouts.header')
            <main class="app-main">
                <div class="page-scroll" data-page-scroll="true">
                    <div class="add-business-page browse-page-shell">
                        <div class="add-business-container">
                            <div class="add-business-wrap">
                                <div class="add-business-card">
                                    <h2 class="add-business-title">Submit a Business</h2>
                                    <p class="add-business-subtitle">Help the community grow by adding businesses that
                                        accept Zcash.</p>

                                    @if (session('success'))
                                        <div class="add-business-alert add-business-alert--success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="add-business-alert add-business-alert--error">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="add-business-form" method="POST"
                                        action="{{ route('add.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-logo-input">Logo / Photo <span
                                                    class="add-business-required">*</span></label>
                                            <div class="add-business-logo-area">
                                                <div class="add-business-logo-preview" id="ab-logo-preview" hidden>
                                                    <img id="ab-logo-preview-image"
                                                        class="add-business-logo-preview-image" alt="Logo preview">
                                                    <button type="button" class="add-business-logo-remove"
                                                        id="ab-logo-remove" aria-label="Remove logo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            aria-hidden="true">
                                                            <path d="M18 6 6 18"></path>
                                                            <path d="m6 6 12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <label class="add-business-upload" id="ab-logo-upload-label"
                                                    for="ab-logo-input">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-upload add-business-upload-icon"
                                                        aria-hidden="true">
                                                        <path d="M12 3v12"></path>
                                                        <path d="m17 8-5-5-5 5"></path>
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    </svg>
                                                    <span class="add-business-upload-text"
                                                        id="ab-logo-upload-text">Upload a photo/logo — required (max 4 MB)</span>
                                                    <input id="ab-logo-input" accept="image/*" name="logo"
                                                        class="add-business-upload-input" type="file" required>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-name">Business Name <span
                                                    class="add-business-required">*</span>
                                            </label>
                                            <input id="ab-name" required autocomplete="organization"
                                                class="add-business-input" placeholder="e.g. Crypto Café Istanbul"
                                                type="text" value="{{ old('name') }}" name="name">
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-cat">Category <span
                                                    class="add-business-required">*</span></label>
                                            <select id="ab-cat" name="category_slug" class="add-business-select" required>
                                                <option value="" disabled @selected(old('category_slug') === null)>Select a category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->slug }}" @selected(old('category_slug') === $category->slug)>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-desc">Description <span
                                                    class="add-business-required">*</span></label>
                                            <p class="add-business-hint">Tell people what this business is and why it
                                                belongs on ZecMap.</p>
                                            <textarea id="ab-desc" name="description" required rows="4"
                                                class="add-business-input" placeholder="Short description of the business...">{{ old('description') }}</textarea>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-community">
                                                {{ __('communities.selectLabel') }}
                                                <span class="add-business-label-hint">({{ __('common.optional') }})</span>
                                            </label>
                                            <p class="add-business-hint">{{ __('addBusiness.communityHint', ['points' => \App\Models\CommunityContribution::POINTS_BUSINESS_COMMUNITY]) }}</p>
                                            <select id="ab-community" name="community_id" class="add-business-select">
                                                <option value="">{{ __('communities.selectNone') }}</option>
                                                @foreach ($communities as $community)
                                                    <option value="{{ $community->id }}" @selected((string) old('community_id') === (string) $community->id)>
                                                        {{ $community->flag_emoji }} {{ $community->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label">Payment
                                                type</label>
                                            <p class="add-business-hint">Choose how this business accepts crypto. This
                                                sets the pin color on the map.</p>
                                            <select id="ab-payment-provider" name="payment_provider"
                                                class="add-business-select" required>
                                                <option value="zcash" @selected(old('payment_provider', 'zcash') === 'zcash')>Zcash</option>
                                                <option value="flexa" @selected(old('payment_provider', 'zcash') === 'flexa')>Flexa</option>
                                            </select>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label">Location on
                                                Map <span class="add-business-required">*</span>
                                            </label>
                                            <p class="add-business-hint">Tap the map to pin the exact location.</p>
                                            <div class="add-business-map-wrap">
                                                <div id="add-business-map" class="add-business-map" role="region"
                                                    aria-label="Location map"></div>
                                            </div>
                                            <input type="hidden" name="lat" id="ab-lat" value="{{ old('lat') }}">
                                            <input type="hidden" name="lng" id="ab-lng" value="{{ old('lng') }}">
                                            <div class="add-business-location-row">
                                                <p class="add-business-location-empty" id="ab-location-status">No location
                                                    selected yet</p>
                                                <button type="button" class="add-business-location-button"
                                                    id="ab-use-location">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                        height="14" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="lucide lucide-locate-fixed"
                                                        aria-hidden="true">
                                                        <line x1="2" x2="5" y1="12"
                                                            y2="12">
                                                        </line>
                                                        <line x1="19" x2="22" y1="12"
                                                            y2="12">
                                                        </line>
                                                        <line x1="12" x2="12" y1="2"
                                                            y2="5">
                                                        </line>
                                                        <line x1="12" x2="12" y1="19"
                                                            y2="22">
                                                        </line>
                                                        <circle cx="12" cy="12" r="7">

                                                        </circle>
                                                        <circle cx="12" cy="12" r="3">

                                                        </circle>
                                                    </svg>
                                                    <span>Use My Location</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-addr">Address <span
                                                    class="add-business-required">*</span>
                                            </label>
                                            <p class="add-business-hint">Start typing to see address suggestions.</p>
                                            <div class="add-business-address-autocomplete">
                                                <input id="ab-addr" required autocomplete="off"
                                                    inputmode="text" class="add-business-input"
                                                    placeholder="Full street address" type="text"
                                                    value="{{ old('address') }}" name="address"
                                                    aria-autocomplete="list" aria-controls="ab-address-suggestions"
                                                    aria-expanded="false">
                                                <div id="ab-address-suggestions"
                                                    class="add-business-address-suggestions" hidden role="listbox"
                                                    aria-label="Address suggestions"></div>
                                            </div>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-email">Contact Email <span
                                                    class="add-business-required">*</span>
                                            </label>
                                            <input id="ab-email" required autocomplete="email"
                                                inputmode="email" class="add-business-input"
                                                placeholder="contact@business.com" type="email"
                                                value="{{ old('email') }}" name="email">
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label" for="ab-zc">Zcash address
                                                (optional)</label>
                                            <input id="ab-zc" inputmode="text" autocorrect="off"
                                                autocapitalize="none" spellcheck="false"
                                                class="add-business-input add-business-input--mono" placeholder=""
                                                type="text" value="{{ old('zcash_address') }}" name="zcash_address">
                                        </div>
                                        <div class="add-business-grid-2">
                                            <div class="add-business-field">
                                                <label class="add-business-label" for="ab-web">Website
                                                    (optional)</label>
                                                <input id="ab-web" inputmode="url" autocomplete="url"
                                                    class="add-business-input" placeholder="https://" type="url"
                                                    value="{{ old('website') }}" name="website">
                                            </div>
                                            <div class="add-business-field">
                                                <label class="add-business-label" for="ab-tw">Twitter/X</label>
                                                <input id="ab-tw" autocorrect="off" autocapitalize="none"
                                                    class="add-business-input" placeholder="username" type="text"
                                                    value="{{ old('twitter') }}" name="twitter">
                                            </div>
                                        </div>
                                        <div class="add-business-field">
                                            <label class="add-business-label">Contact — Phone / Telegram <span
                                                    class="add-business-required">*</span></label>
                                            <p class="add-business-hint">Enter at least one so people can reach the
                                                business — a phone number, a Telegram handle, or both.</p>
                                            <div class="add-business-grid-2">
                                                <div class="add-business-field">
                                                    <label class="add-business-label" for="ab-phone">Phone</label>
                                                    <input id="ab-phone" inputmode="tel" autocomplete="tel"
                                                        class="add-business-input" placeholder="+90 5xx xxx xx xx"
                                                        type="text" value="{{ old('phone') }}" name="phone">
                                                </div>
                                                <div class="add-business-field">
                                                    <label class="add-business-label" for="ab-tg">Telegram</label>
                                                    <input id="ab-tg" autocorrect="off" autocapitalize="none"
                                                        class="add-business-input" placeholder="@username or t.me/…"
                                                        type="text" value="{{ old('telegram') }}" name="telegram">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="add-business-submit">Submit for
                                            Review</button>
                                    </form>
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

    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <span id="ab-address-suggest-url" data-url="{{ route('add.address-suggest') }}" hidden></span>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/add-listing.js') }}" defer></script>
</body>

</html>
