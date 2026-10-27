<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessReview extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'business_id',
        'user_id',
        'rating',
        'body',
        'image_url',
        'community_id',
        'status',
        'admin_note',
        'reviewed_at',
        'reviewed_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Onaylandı',
            self::STATUS_REJECTED => 'Reddedildi',
            default => 'Beklemede',
        };
    }

    public function statusBadgeStyle(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'background: rgba(74, 222, 128, 0.15); color: rgb(74, 222, 128);',
            self::STATUS_REJECTED => 'background: rgba(248, 113, 113, 0.15); color: rgb(248, 113, 113);',
            default => 'background: rgba(251, 191, 36, 0.15); color: rgb(251, 191, 36);',
        };
    }

    public static function blocksNewSubmission(int $businessId, int $userId): bool
    {
        return static::query()
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
            ->exists();
    }

    public function imageDisplayUrl(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        return asset(ltrim($this->image_url, '/'));
    }
}
