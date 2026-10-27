<div class="blog-detail-page">
    <a class="blog-post-back-link" href="{{ route('blog') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-arrow-left" aria-hidden="true">
            <path d="m12 19-7-7 7-7"></path>
            <path d="M19 12H5"></path>
        </svg>
        Back to Blog
    </a>

    @if ($post->featuredImageUrl())
        <div class="blog-post-featured">
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="blog-post-featured-image">
        </div>
    @endif

    <header class="blog-post-header">
        <h1 class="blog-post-title">{{ $post->title }}</h1>
        @if ($post->displayExcerpt())
            <p class="blog-post-excerpt">{{ $post->displayExcerpt() }}</p>
        @endif
        <div class="blog-post-meta">
            @if ($post->formattedPublishedLongDate())
                <span class="blog-post-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-calendar" aria-hidden="true">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                    {{ $post->formattedPublishedLongDate() }}
                </span>
            @endif
            <span class="blog-post-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-clock" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                {{ $post->readingTimeMinutes() }} min read
            </span>
            @if ($post->author_name)
                <span class="blog-post-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-user" aria-hidden="true">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    {{ $post->author_name }}
                </span>
            @endif
        </div>
    </header>

    <div class="blog-post-article">
        {!! $post->content !!}
    </div>

    <footer class="blog-post-footer">
        <a class="blog-post-back-link" href="{{ route('blog') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-arrow-left" aria-hidden="true">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
            </svg>
            Back to Blog
        </a>
    </footer>
</div>
