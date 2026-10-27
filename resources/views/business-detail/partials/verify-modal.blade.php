<div id="biz-verify-modal" class="biz-verify-modal" hidden aria-hidden="true">
    <button type="button" class="biz-verify-modal-backdrop" data-close-verify-modal aria-label="Close"></button>
    <div class="biz-verify-modal-panel" role="dialog" aria-labelledby="biz-verify-modal-title">
        <div class="biz-verify-modal-header">
            <h2 id="biz-verify-modal-title" class="biz-verify-modal-title">Verify Location</h2>
            <button type="button" class="biz-verify-modal-close" data-close-verify-modal aria-label="Close">✕</button>
        </div>

        <form method="POST" action="{{ route('business.verify.store', $business) }}" class="biz-verify-modal-form">
            @csrf
            <p class="biz-verify-modal-intro">
                Help the community confirm that <strong>{{ $business->name }}</strong> accepts crypto at this location.
                @if ($business->verified)
                    You can submit a new verification even if this business is already verified.
                @endif
            </p>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="verify-outdated-info">Outdated information
                    (optional)</label>
                <textarea id="verify-outdated-info" name="outdated_info" rows="3"
                    class="biz-verify-modal-input">{{ old('outdated_info') }}</textarea>
            </div>

            <div class="biz-verify-modal-field">
                <label class="biz-verify-modal-label" for="verify-how-verified">How did you verify? <span
                        aria-hidden="true">*</span></label>
                <textarea id="verify-how-verified" name="how_verified" rows="4" required
                    class="biz-verify-modal-input">{{ old('how_verified') }}</textarea>
            </div>

            <button type="submit" class="biz-verify-modal-submit">Submit verification</button>
        </form>
    </div>
</div>
