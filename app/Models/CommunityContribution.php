<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityContribution extends Model
{
    public const TYPE_BUSINESS = 'business';

    public const TYPE_REVIEW = 'review';

    public const TYPE_COUNTRY_BONUS = 'country_bonus';

    public const POINTS_REVIEW_COMMUNITY = 5;

    public const POINTS_BUSINESS_COMMUNITY = 10;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'community_id',
        'points',
        'contribution_type',
        'user_id',
        'business_id',
        'review_id',
        'country_code',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'integer',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(BusinessReview::class, 'review_id');
    }
}
