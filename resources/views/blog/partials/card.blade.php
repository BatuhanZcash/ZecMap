@php
    /** @var \App\Models\BlogPost $post */
@endphp
<a class="blog-post-card" href="{{ route('blog.show', $post) }}">
    @if ($post->featuredImageUrl())
        <div class="blog-post-card-media">
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="blog-post-card-image"
                loading="lazy">
        </div>
    @endif
    <div class="blog-post-card-body">
        <div class="blog-post-card-content">
            @if ($isLatest ?? false)
                <span class="blog-post-latest-badge">Latest</span>
            @endif
            <h2 class="blog-post-card-title">{{ $post->title }}</h2>
            <p class="blog-post-card-excerpt">{{ $post->displayExcerpt() }}</p>
        </div>
        <div class="blog-post-card-footer">
            <div class="blog-post-card-meta">
                @if ($post->formattedPublishedDate())
                    <span class="blog-post-card-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-calendar" aria-hidden="true">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                            <path d="M3 10h18"></path>
                        </svg>
                        {{ $post->formattedPublishedDate() }}
                    </span>
                @endif
                <span class="blog-post-card-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clock" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6l4 2"></path>
                    </svg>
                    {{ $post->readingTimeMinutes() }} min read
                </span>
            </div>
            <span class="blog-post-card-read-more">
                Read more
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-arrow-right" aria-hidden="true">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </span>
        </div>
    </div>
</a>
