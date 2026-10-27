@extends('layouts.static-page')

@section('title', 'Privacy Policy')
@section('meta_description', __('seo.privacy'))

@section('content')
    <header class="static-page-header static-page-header--legal">
        <span class="static-page-eyebrow">Legal</span>
        <h1 class="static-page-title">Privacy Policy</h1>
        <p class="static-page-meta">
            Last updated: January 2025 · ZecMap is a community project committed to user privacy.
        </p>
    </header>

    <div class="static-page-cards">
        <section class="static-page-card">
            <h2 class="static-page-card-title">Information We Collect</h2>
            <p class="static-page-card-text">
                ZecMap collects only the minimum data required to operate the service. We store the profile
                information you voluntarily provide (display name, avatar, bio) and any business listings you
                submit. We do not collect payment information or link your identity to your Zcash transactions.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">How We Use Your Information</h2>
            <p class="static-page-card-text">
                Profile information you provide is displayed publicly on your profile page and attributed to reviews
                or verifications you submit. We do not sell, rent, or share personal data with third parties for
                marketing purposes.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Analytics</h2>
            <p class="static-page-card-text">
                We collect anonymous, aggregated page-view and session data to understand how the site is used. This
                data contains no personally identifiable information and is stored in our own database — we do not use
                third-party analytics trackers.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Cookies &amp; Local Storage</h2>
            <p class="static-page-card-text">
                ZecMap uses localStorage to persist your language preference and authentication session. No
                third-party cookies are set. You can clear this data at any time through your browser settings.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Business Listings</h2>
            <p class="static-page-card-text">
                Business submissions are reviewed before publication. Submitted data (business name, address, category,
                description) becomes part of the public directory and is visible to all visitors. If you wish to
                remove a listing, contact us via GitHub.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Data Retention</h2>
            <p class="static-page-card-text">
                We retain account data for as long as your account is active. You may request deletion of your account
                and associated data at any time by opening an issue on our GitHub repository.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Open Source</h2>
            <p class="static-page-card-text">
                ZecMap is an open-source community project. Our codebase is publicly available on GitHub. The project
                is built and maintained independently by the community.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Contact</h2>
            <p class="static-page-card-text">
                For privacy-related questions or data deletion requests, please open an issue on our
                <a href="https://github.com/zecmap" target="_blank" rel="noopener noreferrer">GitHub repository</a>.
            </p>
        </section>
    </div>
@endsection
