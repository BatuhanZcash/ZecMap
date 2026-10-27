<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'author_name',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'yayında',
            default => 'taslak',
        };
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function featuredImageUrl(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
            return $this->featured_image;
        }

        return asset(ltrim($this->featured_image, '/'));
    }

    public function displayExcerpt(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return \Illuminate\Support\Str::limit(trim(strip_tags($this->content)), 280);
    }

    public function readingTimeMinutes(): int
    {
        $text = trim(strip_tags($this->content));
        $wordCount = str_word_count($text);

        return max(1, (int) ceil($wordCount / 200));
    }

    public function formattedPublishedDate(): ?string
    {
        return $this->published_at?->format('M j, Y');
    }

    public function formattedPublishedLongDate(): ?string
    {
        return $this->published_at?->format('l, F j, Y');
    }
}
