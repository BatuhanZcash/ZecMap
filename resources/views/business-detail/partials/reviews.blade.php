<div class="biz-reviews-section" id="reviews">
    <h2 class="biz-reviews-title">Reviews &amp; Ratings</h2>

    @if (session('success'))
        <p class="biz-reviews-flash biz-reviews-flash--success" role="status">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p class="biz-reviews-flash biz-reviews-flash--error" role="alert">{{ session('error') }}</p>
    @endif

    @if ($reviews->isNotEmpty())
        <div class="biz-reviews-summary">
            <div class="biz-reviews-summary-score-wrap">
                <span class="biz-reviews-summary-score">{{ number_format((float) $averageRating, 1) }}</span>
                <span class="biz-reviews-summary-max">/5</span>
            </div>
            <div class="biz-reviews-summary-details">
                <div class="biz-reviews-stars" aria-hidden="true">
                    @for ($star = 1; $star <= 5; $star++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            @class([
                                'lucide lucide-star biz-reviews-star',
                                'biz-reviews-star--active' => $star <= round((float) $averageRating),
                            ])>
                            <path
                                d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                            </path>
                        </svg>
                    @endfor
                </div>
                <p class="biz-reviews-summary-count">{{ $reviews->count() }}
                    {{ Str::plural('review', $reviews->count()) }}</p>
            </div>
        </div>
    @endif

    <div class="biz-reviews-form-panel">
        @if ($userReview)
            <p class="biz-reviews-already">You have already reviewed this business.</p>
        @elseif ($hasPendingReview ?? false)
            <p class="biz-reviews-pending">Your review is awaiting admin approval and will be published once approved.</p>
        @else
            <form class="biz-reviews-form" method="POST"
                action="{{ route('business.reviews.store', $business) }}" data-business-reviews-form
                enctype="multipart/form-data">
                @csrf
                <div>
                    <p class="biz-reviews-form-label">Your Rating</p>
                    <div class="biz-reviews-stars" data-review-stars role="radiogroup" aria-label="Your rating">
                        @for ($star = 1; $star <= 5; $star++)
                            <button type="button" class="biz-reviews-star-btn biz-reviews-star-btn--interactive"
                                data-star-value="{{ $star }}" aria-label="{{ $star }} stars">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                    viewBox="0 0 24 24" fill="currentColor" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-star biz-reviews-star" aria-hidden="true">
                                    <path
                                        d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                    </path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" value="{{ old('rating') }}" data-review-rating-input
                        required>
                    @error('rating')
                        <p class="biz-reviews-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="biz-reviews-textarea-wrap">
                    <textarea name="body" maxlength="1000" rows="3" placeholder="Share your experience..."
                        class="biz-reviews-textarea" data-review-body required>{{ old('body') }}</textarea>
                    <span class="biz-reviews-char-count biz-reviews-char-count--overlay"
                        data-review-char-count>0/1000</span>
                    @error('body')
                        <p class="biz-reviews-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="biz-reviews-upload-row">
                    <label class="biz-reviews-image-upload">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-image" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                            <circle cx="9" cy="9" r="2"></circle>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                        </svg>
                        Add photo
                        <input type="file" name="image" accept="image/*" class="biz-reviews-image-input"
                            data-review-image-input>
                    </label>
                </div>
                @error('image')
                    <p class="biz-reviews-field-error">{{ $message }}</p>
                @enderror
                <div class="biz-reviews-country-field">
                    <label class="biz-reviews-country-label" for="review-community">
                        Community <span class="biz-reviews-country-label-hint">(optional)</span>
                    </label>
                    <p class="biz-reviews-country-hint">Support a regional group — your review adds +5 contribution
                        points to the selected community.</p>
                    <select id="review-community" name="community_id" class="biz-reviews-country-select">
                        <option value="">No community selected</option>
                        @foreach ($communities as $community)
                            <option value="{{ $community->id }}" @selected((string) old('community_id') === (string) $community->id)>
                                {{ $community->flag_emoji }} {{ $community->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('community_id')
                        <p class="biz-reviews-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="biz-reviews-submit-row">
                    <button type="submit" disabled class="biz-reviews-submit" data-review-submit>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-send" aria-hidden="true">
                            <path
                                d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z">
                            </path>
                            <path d="m21.854 2.147-10.94 10.939"></path>
                        </svg>
                        Post Review
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($reviews->isEmpty())
        <p class="biz-reviews-empty">No reviews yet. Be the first!</p>
    @else
        <div class="biz-reviews-list">
            @foreach ($reviews as $review)
                <article @class(['biz-reviews-card', 'biz-reviews-card--own' => auth()->id() === $review->user_id])>
                    <header class="biz-reviews-card-header">
                        <div class="biz-reviews-card-author">
                            <span class="biz-reviews-card-avatar">{{ $review->user->initials() }}</span>
                            <div class="biz-reviews-card-meta">
                                <span class="biz-reviews-card-name">{{ $review->user->username }}</span>
                                <time class="biz-reviews-card-date"
                                    datetime="{{ $review->created_at?->toIso8601String() }}">
                                    {{ $review->created_at?->format('M j, Y') }}
                                </time>
                            </div>
                        </div>
                        <div class="biz-reviews-stars" aria-label="{{ $review->rating }} out of 5 stars">
                            @for ($star = 1; $star <= 5; $star++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="currentColor" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    @class([
                                        'lucide lucide-star biz-reviews-star',
                                        'biz-reviews-star--active' => $star <= $review->rating,
                                    ])>
                                    <path
                                        d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                    </path>
                                </svg>
                            @endfor
                        </div>
                    </header>
                    @if ($review->community)
                        <p class="biz-reviews-card-community">
                            <span aria-hidden="true">{{ $review->community->flag_emoji ?: '🌍' }}</span>
                            {{ $review->community->name }}
                        </p>
                    @endif
                    <p class="biz-reviews-card-body">{{ $review->body }}</p>
                    @if ($review->imageDisplayUrl())
                        <a class="biz-reviews-card-image" href="{{ $review->imageDisplayUrl() }}" target="_blank"
                            rel="noopener noreferrer">
                            <img src="{{ $review->imageDisplayUrl() }}" alt="Review photo from {{ $review->user->username }}"
                                class="biz-reviews-card-image-img" loading="lazy">
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
