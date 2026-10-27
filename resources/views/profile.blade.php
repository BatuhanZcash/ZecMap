<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — My Profile</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => __('nav.profile') . ' — ' . config('app.name', 'ZecMap'),
            'description' => __('seo.profile'),
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
                    <div class="profile-page browse-page-shell">
                        <div class="profile-max-width mx-auto w-full px-4 sm:px-6 md:px-10 lg:px-12"
                            style="max-width: 640px; margin: 0 auto; padding: 5rem 1rem 3rem;">

                            @if ($guest ?? false)
                                <div class="profile-guest">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-user profile-guest-icon" aria-hidden="true">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <h1 class="profile-guest-title">Not signed in</h1>
                                    <p class="profile-guest-subtitle">Sign in to see your profile and contributions.</p>
                                    <a href="{{ route('auth') }}" class="profile-signin-button">Sign In</a>
                                </div>
                            @else
                                @if (session('success'))
                                    <div class="auth-error"
                                        style="margin-bottom: 1rem; color: rgb(74, 222, 128); border-color: rgba(74, 222, 128, 0.25); background: rgba(74, 222, 128, 0.12);"
                                        role="status">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="profile-hero-card">
                                    <div class="profile-avatar-wrap">
                                        <div class="profile-avatar">
                                            @if ($avatarUrl)
                                                <img alt="" class="profile-avatar-image" data-profile-avatar-preview
                                                    src="{{ $avatarUrl }}">
                                                <span class="profile-avatar-initials" data-profile-avatar-initials hidden>{{ $initial }}</span>
                                            @else
                                                <img alt="" class="profile-avatar-image" data-profile-avatar-preview hidden>
                                                <span class="profile-avatar-initials" data-profile-avatar-initials>{{ $initial }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="profile-info">
                                        <div data-profile-display @class(['is-hidden' => $errors->any()])>
                                            <div class="profile-info-header">
                                                <h1 class="profile-display-name">{{ $user->displayName() }}</h1>
                                                <button type="button" class="profile-edit-button" data-profile-edit
                                                    title="Edit" aria-label="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-pencil" aria-hidden="true">
                                                        <path
                                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z">
                                                        </path>
                                                        <path d="m15 5 4 4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="profile-handle">{{ '@'.$username }}</p>
                                            @if ($user->bio)
                                                <p class="profile-bio">{{ $user->bio }}</p>
                                            @else
                                                <p class="profile-bio profile-bio--empty">No bio yet — click ✏️ to add one</p>
                                            @endif
                                        </div>

                                        <form class="profile-edit-form" method="POST" action="{{ route('profile.update') }}"
                                            enctype="multipart/form-data" data-profile-edit-form
                                            @class(['is-open' => $errors->any()])>
                                            @csrf
                                            @method('PUT')

                                            @if ($errors->any())
                                                <div class="auth-error" role="alert" style="margin-bottom: 0.75rem;">
                                                    {{ $errors->first() }}
                                                </div>
                                            @endif

                                            <label class="profile-field-label" for="profile-name">Display name</label>
                                            <input id="profile-name" name="name" class="profile-input" maxlength="40"
                                                required value="{{ old('name', $user->displayName()) }}"
                                                placeholder="Display name (max 40 chars)">

                                            <label class="profile-field-label" for="profile-bio">Bio</label>
                                            <textarea id="profile-bio" name="bio" class="profile-textarea" maxlength="280"
                                                rows="3" placeholder="Tell us about yourself (max 280 chars)">{{ old('bio', $user->bio) }}</textarea>
                                            <label class="profile-field-label" for="profile-payout" style="margin-top:1rem">Zcash payout address <span style="opacity:.6;font-weight:400">(for ZecMoon prizes)</span></label>
                                            <input id="profile-payout" name="payout_address" class="profile-input" maxlength="255"
                                                value="{{ old('payout_address', $user->payout_address) }}"
                                                placeholder="u1... / zs... / t1...  — where weekly prizes are sent">

                                            <div class="profile-edit-actions">
                                                <span class="profile-char-count" data-profile-bio-count>{{ strlen(old('bio', $user->bio ?? '')) }}/280</span>
                                                <div class="profile-edit-buttons">
                                                    <button type="button" class="profile-button profile-button--ghost"
                                                        data-profile-cancel>Cancel</button>
                                                    <button type="submit" class="profile-button profile-button--primary">Save Profile</button>
                                                </div>
                                            </div>

                                            <div style="margin-top: 1rem;">
                                                <label class="profile-field-label" for="profile-avatar-input">Avatar</label>
                                                <input id="profile-avatar-input" name="avatar" type="file" accept="image/*"
                                                    class="profile-input">
                                                <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                                    <input type="checkbox" name="remove_avatar" value="1">
                                                    Remove avatar
                                                </label>
                                            </div>
                                        </form>

                                        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid rgba(127,127,127,0.15)">
                                            <p class="profile-field-label" style="font-weight:700;margin-bottom:.6rem">Change password</p>
                                            @if (session('password_status'))
                                                <div class="auth-error" role="status" style="margin-bottom:.6rem;background:rgba(34,197,94,.12);color:#22c55e;border-color:rgba(34,197,94,.3)">
                                                    {{ session('password_status') }}
                                                </div>
                                            @endif
                                            @if ($errors->getBag('password')->any())
                                                <div class="auth-error" role="alert" style="margin-bottom:.6rem">
                                                    {{ $errors->getBag('password')->first() }}
                                                </div>
                                            @endif
                                            <form method="POST" action="{{ route('profile.password') }}">
                                                @csrf
                                                @method('PUT')
                                                <label class="profile-field-label" for="pw-current">Current password</label>
                                                <input id="pw-current" name="current_password" type="password" class="profile-input"
                                                    autocomplete="current-password" placeholder="Your current password">
                                                <label class="profile-field-label" for="pw-new" style="margin-top:.6rem">New password</label>
                                                <input id="pw-new" name="password" type="password" class="profile-input"
                                                    autocomplete="new-password" minlength="6" placeholder="At least 6 characters">
                                                <label class="profile-field-label" for="pw-confirm" style="margin-top:.6rem">Confirm new password</label>
                                                <input id="pw-confirm" name="password_confirmation" type="password" class="profile-input"
                                                    autocomplete="new-password" minlength="6" placeholder="Repeat new password">
                                                <div style="margin-top:.8rem">
                                                    <button type="submit" class="profile-button profile-button--primary">Update password</button>
                                                </div>
                                            </form>
                                        </div>

                                        <form method="POST" action="{{ route('auth.logout') }}" style="margin-top: 1rem;">
                                            @csrf
                                            <button type="submit" class="profile-signout-button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-log-out" aria-hidden="true">
                                                    <path d="m16 17 5-5-5-5"></path>
                                                    <path d="M21 12H9"></path>
                                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                </svg>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="profile-stats-grid">
                                    <div class="profile-stat-card">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-map-pin-check profile-stat-icon" aria-hidden="true">
                                            <path
                                                d="M19.43 12.935c.357-.967.57-1.955.57-2.935a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 1.202 0 32.197 32.197 0 0 0 .813-.728">
                                            </path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                            <path d="m16 18 2 2 4-4"></path>
                                        </svg>
                                        <div class="profile-stat-value">{{ number_format($verificationCount) }}</div>
                                        <div class="profile-stat-label">Verifications</div>
                                    </div>
                                    <div class="profile-stat-card">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-star profile-stat-icon" aria-hidden="true">
                                            <path
                                                d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                            </path>
                                        </svg>
                                        <div class="profile-stat-value">{{ number_format($reviewCount) }}</div>
                                        <div class="profile-stat-label">Reviews</div>
                                    </div>
                                    <div class="profile-stat-card">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="profile-stat-icon" aria-hidden="true">
                                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                                        </svg>
                                        <div class="profile-stat-value">{{ number_format($zecmoonCount ?? 0) }}</div>
                                        <div class="profile-stat-label">ZecMoons</div>
                                    </div>
                                </div>

                                <section class="profile-section">
                                    <a href="{{ route('saved') }}" class="profile-saved-link">
                                        <span class="profile-saved-link-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-bookmark">
                                                <path d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z"></path>
                                            </svg>
                                        </span>
                                        <span class="profile-saved-link-text">{{ __('profile.savedBusinesses') }}</span>
                                        @if ($savedCount > 0)
                                            <span class="profile-saved-link-count">{{ $savedCount }}</span>
                                        @endif
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-chevron-right profile-saved-link-chevron"
                                            aria-hidden="true">
                                            <path d="m9 18 6-6-6-6"></path>
                                        </svg>
                                    </a>
                                </section>

                                <section class="profile-section profile-reviews-section">
                                    <div class="profile-section-header">
                                        <h2 class="profile-section-title">My Reviews</h2>
                                        @if ($reviewCount > 0)
                                            <span class="profile-reviews-count">{{ $reviewCount }}
                                                {{ Str::plural('review', $reviewCount) }}</span>
                                        @endif
                                    </div>

                                    @if ($reviews->isEmpty())
                                        <div class="profile-reviews-empty">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-message-square profile-reviews-empty-icon"
                                                aria-hidden="true">
                                                <path
                                                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                                </path>
                                            </svg>
                                            <p class="profile-reviews-empty-title">No reviews yet</p>
                                            <p class="profile-reviews-empty-text">Visit a business page and share your
                                                experience to see it here.</p>
                                            <a class="profile-reviews-empty-link" href="{{ url('/browse') }}">Browse
                                                businesses</a>
                                        </div>
                                    @else
                                        <ul class="profile-review-list">
                                            @foreach ($reviews as $review)
                                                @if ($review->business)
                                                    <li class="profile-review-card">
                                                        <a class="profile-review-business"
                                                            href="{{ route('business.show', $review->business) }}">
                                                            @if ($review->business->logoDisplayUrl())
                                                                <img alt="" class="profile-review-business-logo"
                                                                    src="{{ $review->business->logoDisplayUrl() }}">
                                                            @else
                                                                <span
                                                                    class="profile-review-business-logo profile-review-business-logo--placeholder"
                                                                    aria-hidden="true">🏪</span>
                                                            @endif
                                                            <span class="profile-review-business-info">
                                                                <span
                                                                    class="profile-review-business-name">{{ $review->business->name }}</span>
                                                                <span class="profile-review-business-category">
                                                                    {{ $review->business->category?->name ?? 'Business' }}
                                                                </span>
                                                            </span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-chevron-right profile-review-business-chevron"
                                                                aria-hidden="true">
                                                                <path d="m9 18 6-6-6-6"></path>
                                                            </svg>
                                                        </a>

                                                        <div class="profile-review-rating-row">
                                                            <div class="profile-review-stars"
                                                                aria-label="{{ $review->rating }} out of 5 stars">
                                                                @for ($star = 1; $star <= 5; $star++)
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                        height="14" viewBox="0 0 24 24" fill="currentColor"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        @class([
                                                                            'lucide lucide-star profile-review-star',
                                                                            'profile-review-star--active' => $star <= $review->rating,
                                                                        ])>
                                                                        <path
                                                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                                                        </path>
                                                                    </svg>
                                                                @endfor
                                                            </div>
                                                            @if ($review->created_at)
                                                                <time class="profile-review-date"
                                                                    datetime="{{ $review->created_at->toIso8601String() }}">
                                                                    {{ $review->created_at->format('M j, Y') }}
                                                                </time>
                                                            @endif
                                                        </div>

                                                        @if ($review->community)
                                                            <p class="profile-review-community">
                                                                <span aria-hidden="true">{{ $review->community->flag_emoji ?: '🌍' }}</span>
                                                                {{ $review->community->name }}
                                                            </p>
                                                        @endif

                                                        <p class="profile-review-body">{{ $review->body }}</p>

                                                        @if ($review->imageDisplayUrl())
                                                            <a class="profile-review-image"
                                                                href="{{ $review->imageDisplayUrl() }}" target="_blank"
                                                                rel="noopener noreferrer">
                                                                <img src="{{ $review->imageDisplayUrl() }}"
                                                                    alt="Review photo for {{ $review->business->name }}"
                                                                    class="profile-review-image-img" loading="lazy">
                                                            </a>
                                                        @endif

                                                        <a class="profile-review-view-link"
                                                            href="{{ route('business.show', $review->business) }}#reviews">
                                                            View on business page
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13"
                                                                height="13" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-arrow-up-right" aria-hidden="true">
                                                                <path d="M7 7h10v10"></path>
                                                                <path d="M7 17 17 7"></path>
                                                            </svg>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </section>

                                @if ($verifications->isNotEmpty())
                                    <section class="profile-section">
                                        <h2 class="profile-section-title">Recent Verifications</h2>
                                        <ul class="profile-verification-list">
                                            @foreach ($verifications as $verification)
                                                @if ($verification->business)
                                                    <li>
                                                        <a class="profile-verification-item"
                                                            href="{{ route('business.show', $verification->business) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-map-pin-check profile-verification-icon"
                                                                aria-hidden="true">
                                                                <path
                                                                    d="M19.43 12.935c.357-.967.57-1.955.57-2.935a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 1.202 0 32.197 32.197 0 0 0 .813-.728">
                                                                </path>
                                                                <circle cx="12" cy="10" r="3"></circle>
                                                                <path d="m16 18 2 2 4-4"></path>
                                                            </svg>
                                                            <span class="profile-verification-name">{{ $verification->business->name }}</span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-chevron-right profile-verification-chevron"
                                                                aria-hidden="true">
                                                                <path d="m9 18 6-6-6-6"></path>
                                                            </svg>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </section>
                                @endif

                                @if ($businesses->isNotEmpty())
                                    <section class="profile-section">
                                        <div class="profile-section-header">
                                            <h2 class="profile-section-title">My Businesses</h2>
                                            <a class="profile-section-link" href="{{ url('/add') }}">+ Add</a>
                                        </div>
                                        <ul class="profile-business-list">
                                            @foreach ($businesses as $business)
                                                <li class="profile-business-item">
                                                    @if ($business->logoDisplayUrl())
                                                        <img alt="" class="profile-business-logo"
                                                            src="{{ $business->logoDisplayUrl() }}">
                                                    @else
                                                        <span
                                                            class="profile-business-logo profile-business-logo--placeholder"
                                                            aria-hidden="true">🏪</span>
                                                    @endif
                                                    <div class="profile-business-info">
                                                        <p class="profile-business-name">{{ $business->name }}</p>
                                                        <p class="profile-business-category">
                                                            {{ $business->category?->name ?? 'Business' }}
                                                        </p>
                                                    </div>
                                                    <div class="profile-business-actions">
                                                        @if ($business->verified)
                                                            <span class="profile-business-verified" title="Verified">✓</span>
                                                        @endif
                                                        <span class="profile-business-views">
                                                            {{ number_format($business->view_count) }} views
                                                        </span>
                                                        <a class="profile-business-detail-link"
                                                            href="{{ route('business.show', $business) }}"
                                                            aria-label="View {{ $business->name }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-chevron-right" aria-hidden="true">
                                                                <path d="m9 18 6-6-6-6"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </section>
                                @endif

                            @endif
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
    @unless ($guest ?? true)
        <script src="{{ asset('assets/js/profile.js') }}" defer></script>
    @endunless
</body>

</html>
