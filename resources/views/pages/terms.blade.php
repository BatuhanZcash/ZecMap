@extends('layouts.static-page')

@section('title', 'Terms of Service')
@section('meta_description', __('seo.terms'))

@section('content')
    <header class="static-page-header static-page-header--legal">
        <span class="static-page-eyebrow">Legal</span>
        <h1 class="static-page-title">Terms of Service</h1>
        <p class="static-page-meta">
            Last updated: January 2025 · Please read these terms carefully before using ZecMap.
        </p>
    </header>

    <div class="static-page-cards">
        <section class="static-page-card">
            <h2 class="static-page-card-title">Acceptance of Terms</h2>
            <p class="static-page-card-text">
                By accessing or using ZecMap ("the Service"), you agree to be bound by these Terms of Service. If you
                do not agree, please do not use the Service. ZecMap is an open-source, community-run project provided
                free of charge.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Use of the Service</h2>
            <p class="static-page-card-text">
                You may use ZecMap to discover, submit, review, and verify businesses that accept Zcash. You agree not
                to submit false or misleading information, spam listings, harass other users, or use the Service for
                any unlawful purpose. We reserve the right to remove content or accounts that violate these terms.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">User Accounts</h2>
            <p class="static-page-card-text">
                You are responsible for maintaining the confidentiality of your account credentials. You agree to
                notify us immediately of any unauthorized use of your account. We are not liable for any loss or damage
                arising from your failure to protect your account.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">User-Generated Content</h2>
            <p class="static-page-card-text">
                By submitting a business listing, review, or verification, you grant ZecMap a non-exclusive, royalty-free,
                perpetual license to display and distribute that content as part of the Service. You represent that you
                have the right to submit such content and that it does not infringe any third-party rights.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Accuracy of Information</h2>
            <p class="static-page-card-text">
                ZecMap does not guarantee the accuracy, completeness, or timeliness of business listings. Listings are
                community-submitted and may not reflect current real-world conditions. Always verify payment acceptance
                directly with a merchant before visiting.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Intellectual Property</h2>
            <p class="static-page-card-text">
                Map data is © OpenStreetMap contributors, licensed under ODbL. Business listing data submitted by users
                is considered community-contributed content. ZecMap is an independent community-driven project.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Disclaimer of Warranties</h2>
            <p class="static-page-card-text">
                The Service is provided "as is" without warranties of any kind, express or implied. We do not warrant that
                the Service will be uninterrupted, error-free, or free of harmful components. Use of the Service is at
                your own risk.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Limitation of Liability</h2>
            <p class="static-page-card-text">
                To the fullest extent permitted by law, ZecMap and its contributors shall not be liable for any indirect,
                incidental, special, or consequential damages arising from your use of the Service, including but not
                limited to reliance on business listings or transactions made with listed merchants.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Changes to Terms</h2>
            <p class="static-page-card-text">
                We reserve the right to update these Terms at any time. Continued use of the Service after changes are
                posted constitutes acceptance of the new Terms. Significant changes will be announced via the ZecMap
                GitHub repository.
            </p>
        </section>

        <section class="static-page-card">
            <h2 class="static-page-card-title">Contact</h2>
            <p class="static-page-card-text">
                For questions regarding these Terms, please open an issue on our
                <a href="https://github.com/zecmap" target="_blank" rel="noopener noreferrer">GitHub repository</a>.
                See also our <a href="{{ route('privacy') }}">Privacy Policy</a>.
            </p>
        </section>
    </div>
@endsection
