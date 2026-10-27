<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Community extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'flag_emoji',
        'country_code',
    ];

    /**
     * Active businesses located in this community's country.
     * Auto derived by region (country_code), not by manual linking.
     *
     * @return array{0:\Illuminate\Support\Collection,1:int}
     */
    public function businessesInRegion(int $limit = 12): array
    {
        if (empty($this->country_code)) {
            return [collect(), 0];
        }

        $query = \App\Models\Business::query()
            ->with('category:id,name,slug')
            ->where('country_code', $this->country_code)
            ->where('status', \App\Models\Business::STATUS_ACTIVE)
            // Only local listings. Bulk imported (Flexa) spots are not repeated here;
            // they already appear on the main explore map.
            ->where(function ($q) {
                $q->where('payment_provider', '!=', \App\Models\Business::PAYMENT_FLEXA)
                    ->orWhereNull('payment_provider');
            });

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('verified')->orderBy('name')->limit($limit)->get();

        return [$rows, $total];
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(CommunityContribution::class);
    }

    public function pointsSince(?Carbon $since = null): int
    {
        $query = $this->contributions();

        if ($since !== null) {
            $query->where('created_at', '>=', $since);
        }

        return (int) $query->sum('points');
    }

    public function scoreWeekly(): int
    {
        return $this->pointsSince(now()->startOfWeek());
    }

    public function scoreMonthly(): int
    {
        return $this->pointsSince(now()->startOfMonth());
    }

    public function scoreYearly(): int
    {
        return $this->pointsSince(now()->startOfYear());
    }

    public function scoreTotal(): int
    {
        return $this->pointsSince(null);
    }

    public function scoreForPeriod(string $period): int
    {
        return match ($period) {
            'weekly' => $this->scoreWeekly(),
            'yearly' => $this->scoreYearly(),
            'all' => $this->scoreTotal(),
            default => $this->scoreMonthly(),
        };
    }

    /**
     * @return array<string, int>
     */
    public function scores(): array
    {
        return [
            'weekly' => $this->scoreWeekly(),
            'monthly' => $this->scoreMonthly(),
            'yearly' => $this->scoreYearly(),
            'all' => $this->scoreTotal(),
        ];
    }

    public function businessContributionsCount(): int
    {
        return (int) $this->contributions()
            ->where('contribution_type', CommunityContribution::TYPE_BUSINESS)
            ->count();
    }

    public function contributionsCount(): int
    {
        return (int) $this->contributions()->count();
    }

    public function formattedScore(int $points): string
    {
        return ($points >= 0 ? '+' : '').number_format($points);
    }
}
