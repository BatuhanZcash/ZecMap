<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_CLOSED = 'closed';

    public const PAYMENT_ZCASH = 'zcash';

    public const PAYMENT_FLEXA = 'flexa';

    public const BADGE_CUSTOM = 'custom';

    /**
     * @return array<string, array{label: string, class: string}>
     */
    public static function badgePresets(): array
    {
        return [
            'sponsor' => ['label' => 'Sponsor', 'class' => 'promo'],
            'new' => ['label' => 'New', 'class' => 'new'],
            'featured' => ['label' => 'Featured', 'class' => 'featured'],
            'popular' => ['label' => 'Popular', 'class' => 'popular'],
            'top_pick' => ['label' => 'Top Pick', 'class' => 'gold'],
        ];
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'name',
        'category_id',
        'description',
        'address',
        'lat',
        'lng',
        'verified',
        'logo_url',
        'status',
        'phone',
        'website',
        'zcash_address',
        'city',
        'hours',
        'payment_options',
        'social_links',
        'owner_id',
        'view_count',
        'payment_provider',
        'community_id',
        'country_code',
        'business_json_import_id',
        'sort_order',
        'badge_key',
        'badge_label',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'verified' => 'boolean',
            'hours' => 'array',
            'payment_options' => 'array',
            'social_links' => 'array',
            'view_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return array{label: string, class: string}|null
     */
    public function browseBadge(): ?array
    {
        if ($this->badge_key === null || $this->badge_key === '') {
            return null;
        }

        if ($this->badge_key === self::BADGE_CUSTOM) {
            $label = trim((string) $this->badge_label);

            if ($label === '') {
                return null;
            }

            return [
                'label' => $label,
                'class' => 'custom',
            ];
        }

        $preset = self::badgePresets()[$this->badge_key] ?? null;

        return $preset ? [
            'label' => $preset['label'],
            'class' => $preset['class'],
        ] : null;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeOrderedForBrowse($query, string $secondarySort = 'newest')
    {
        $query->orderByRaw('CASE WHEN sort_order = 0 THEN 1 ELSE 0 END')
            ->orderBy('sort_order');

        return match ($secondarySort) {
            'verified' => $query->orderByDesc('verified')->orderBy('name'),
            'az' => $query->orderBy('name'),
            'za' => $query->orderByDesc('name'),
            default => $query->orderByDesc('created_at'),
        };
    }

    public function jsonImport(): BelongsTo
    {
        return $this->belongsTo(BusinessJsonImport::class, 'business_json_import_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class)->approved();
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_favorites')
            ->withTimestamps();
    }

    public function latestApprovedVerification(): ?VerificationRequest
    {
        return $this->verificationRequests()
            ->where('status', VerificationRequest::STATUS_APPROVED)
            ->whereNotNull('reviewed_at')
            ->with('user:id,name')
            ->orderByDesc('reviewed_at')
            ->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function fullAddress(): string
    {
        return trim(implode(', ', array_filter([$this->address, $this->city])));
    }

    public function googleMapsDirectionsUrl(): ?string
    {
        if ($this->lat === null || $this->lng === null) {
            return null;
        }

        return 'https://www.google.com/maps/dir/?api=1&destination='.$this->lat.','.$this->lng;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function logoDisplayUrl(): ?string
    {
        if (! $this->logo_url) {
            return null;
        }

        if (str_starts_with($this->logo_url, 'http://') || str_starts_with($this->logo_url, 'https://')) {
            return $this->logo_url;
        }

        return asset(ltrim($this->logo_url, '/'));
    }

    public function statusBadgeStyle(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'background: rgba(74, 222, 128, 0.15); color: rgb(74, 222, 128);',
            self::STATUS_CLOSED => 'background: rgba(248, 113, 113, 0.15); color: rgb(248, 113, 113);',
            default => 'background: rgba(156, 163, 175, 0.15); color: rgb(156, 163, 175);',
        };
    }

    public function contactEmail(): ?string
    {
        $email = $this->social_links['email'] ?? null;

        if (is_string($email) && trim($email) !== '') {
            return trim($email);
        }

        return $this->owner?->email;
    }

    public function moderationStatus(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'approved',
            self::STATUS_CLOSED => 'rejected',
            default => 'pending',
        };
    }

    public function moderationStatusLabel(): string
    {
        return match ($this->moderationStatus()) {
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            default => 'Beklemede',
        };
    }

    public function moderationStatusBadgeStyle(): string
    {
        return match ($this->moderationStatus()) {
            'approved' => 'background: rgba(74, 222, 128, 0.15); color: rgb(74, 222, 128);',
            'rejected' => 'background: rgba(248, 113, 113, 0.15); color: rgb(248, 113, 113);',
            default => 'background: rgba(255, 193, 7, 0.15); color: rgb(255, 193, 7);',
        };
    }
}
