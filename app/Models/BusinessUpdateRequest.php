<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessUpdateRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Human labels for the fields a request can propose to change.
     *
     * @return array<string, string>
     */
    public static function fieldLabels(): array
    {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'website' => 'Website',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'hours' => 'Opening hours',
            'social_links' => 'Social links',
        ];
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'business_id',
        'user_id',
        'changes',
        'proposed_logo_url',
        'wants_wallet_change',
        'message',
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
            'changes' => 'array',
            'wants_wallet_change' => 'boolean',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
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
}
