<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BlogPost */
class BlogPostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->displayExcerpt(),
            'content' => $this->content,
            'content_format' => $this->contentFormat(),
            'featured_image' => $this->featuredImageUrl(),
            'author_name' => $this->author_name,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'read_time_minutes' => $this->readingTimeMinutes(),
        ];
    }

    private function contentFormat(): string
    {
        $trimmed = trim((string) $this->content);
        if ($trimmed === '') {
            return 'markdown';
        }
        if (str_starts_with($trimmed, '<')) {
            return 'html';
        }

        return 'markdown';
    }
}
