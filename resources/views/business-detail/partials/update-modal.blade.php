@php
    $bizSocial = is_array($business->social_links) ? $business->social_links : [];
@endphp
<div id="biz-update-modal" class="biz-verify-modal biz-update-modal" hidden aria-hidden="true">
    <button type="button" class="biz-verify-modal-backdrop" data-close-update-modal aria-label="Close"></button>
    <div class="biz-verify-modal-panel" role="dialog" aria-labelledby="biz-update-modal-title">
        <div class="biz-verify-modal-header">
            <h2 id="biz-update-modal-title" class="biz-verify-modal-title">Update this business</h2>
            <button type="button" class="biz-verify-modal-close" data-close-update-modal aria-label="Close">✕</button>
        </div>

        <form method="POST" action="{{ route('business.update-request.store', $business) }}"
            class="biz-verify-modal-form" enctype="multipart/form-data">
            @csrf
            <p class="biz-verify-modal-intro">
                Suggest updated details for <strong>{{ $business->name }}</strong>. Leave a field blank to keep it as
                it is. Every change is reviewed by our team before it goes live.
            </p>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-name">Name</label>
                <input id="update-name" name="name" type="text" class="biz-verify-modal-input"
                    value="{{ old('name') }}" placeholder="{{ $business->name }}">
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-description">Description</label>
                <textarea id="update-description" name="description" rows="3"
                    class="biz-verify-modal-input" placeholder="{{ \Illuminate\Support\Str::limit(strip_tags((string) $business->description), 120) }}">{{ old('description') }}</textarea>
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-website">Website</label>
                <input id="update-website" name="website" type="text" class="biz-verify-modal-input"
                    value="{{ old('website') }}" placeholder="{{ $business->website ?: 'https://' }}">
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-phone">Phone</label>
                <input id="update-phone" name="phone" type="text" class="biz-verify-modal-input"
                    value="{{ old('phone') }}" placeholder="{{ $business->phone ?: '+90 ...' }}">
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-address">Address</label>
                <input id="update-address" name="address" type="text" class="biz-verify-modal-input"
                    value="{{ old('address') }}" placeholder="{{ $business->address }}">
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-city">City</label>
                <input id="update-city" name="city" type="text" class="biz-verify-modal-input"
                    value="{{ old('city') }}" placeholder="{{ $business->city }}">
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-hours">Opening hours</label>
                <textarea id="update-hours" name="hours" rows="2" class="biz-verify-modal-input"
                    placeholder="Mon to Fri 09:00 to 18:00, Sat 10:00 to 14:00">{{ old('hours') }}</textarea>
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-logo">Logo or photo</label>
                <input id="update-logo" name="logo" type="file" class="biz-verify-modal-input"
                    accept="image/jpeg,image/png,image/webp">
                <small class="biz-update-hint">JPG, PNG or WebP, up to 4 MB.</small>
            </div>

            <p class="biz-update-group-title">Social links</p>
            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-twitter">X / Twitter</label>
                <input id="update-twitter" name="social_twitter" type="text" class="biz-verify-modal-input"
                    value="{{ old('social_twitter') }}" placeholder="{{ $bizSocial['twitter'] ?? ($bizSocial['x'] ?? 'https://x.com/...') }}">
            </div>
            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-instagram">Instagram</label>
                <input id="update-instagram" name="social_instagram" type="text" class="biz-verify-modal-input"
                    value="{{ old('social_instagram') }}" placeholder="{{ $bizSocial['instagram'] ?? 'https://instagram.com/...' }}">
            </div>
            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-telegram">Telegram</label>
                <input id="update-telegram" name="social_telegram" type="text" class="biz-verify-modal-input"
                    value="{{ old('social_telegram') }}" placeholder="{{ $bizSocial['telegram'] ?? 'https://t.me/...' }}">
            </div>
            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-facebook">Facebook</label>
                <input id="update-facebook" name="social_facebook" type="text" class="biz-verify-modal-input"
                    value="{{ old('social_facebook') }}" placeholder="{{ $bizSocial['facebook'] ?? 'https://facebook.com/...' }}">
            </div>
            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-email">Contact email</label>
                <input id="update-email" name="social_email" type="email" class="biz-verify-modal-input"
                    value="{{ old('social_email') }}" placeholder="{{ $bizSocial['email'] ?? 'name@example.com' }}">
            </div>

            <div class="biz-update-wallet-box">
                <p class="biz-update-group-title">Payment (ZEC) address</p>
                <p class="biz-update-wallet-note">
                    For security we never change a payment address from a public form. Tick the box below, then reach
                    us from this business's listed email, website or Telegram so we can confirm you are the owner.
                </p>
                <label class="biz-update-check">
                    <input type="checkbox" name="wants_wallet_change" value="1" @checked(old('wants_wallet_change'))>
                    <span>I want to update the payment address and will contact you from the listed channel.</span>
                </label>
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="update-message">Message to the team (optional)</label>
                <textarea id="update-message" name="message" rows="2" class="biz-verify-modal-input"
                    placeholder="Anything else we should know">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="biz-verify-modal-submit">Send update request</button>
        </form>
    </div>
</div>

<style>
    .biz-update-modal .biz-verify-modal-panel { max-height: 88vh; overflow-y: auto; }
    .biz-update-group-title { margin: 18px 0 6px; font-weight: 700; font-size: 13px; opacity: .8; letter-spacing: .02em; text-transform: uppercase; }
    .biz-update-hint { display: block; margin-top: 5px; font-size: 12px; opacity: .65; }
    .biz-update-wallet-box { margin-top: 16px; padding: 14px; border-radius: 12px; background: rgba(243, 184, 38, .10); border: 1px solid rgba(243, 184, 38, .30); }
    .biz-update-wallet-box .biz-update-group-title { margin-top: 0; }
    .biz-update-wallet-note { font-size: 13px; line-height: 1.5; opacity: .85; margin-bottom: 10px; }
    .biz-update-check { display: flex; gap: 9px; align-items: flex-start; font-size: 13.5px; line-height: 1.45; cursor: pointer; }
    .biz-update-check input { margin-top: 3px; flex: none; }
</style>

<script>
    (function () {
        var modal = document.getElementById('biz-update-modal');
        if (!modal) return;
        function open() { modal.hidden = false; modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
        function close() { modal.hidden = true; modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
        document.querySelectorAll('[data-open-update-modal]').forEach(function (b) {
            b.addEventListener('click', function (e) { e.preventDefault(); open(); });
        });
        modal.querySelectorAll('[data-close-update-modal]').forEach(function (b) {
            b.addEventListener('click', function (e) { e.preventDefault(); close(); });
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });
    })();
</script>
