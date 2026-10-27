<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommunityRequest;
use App\Models\Community;
use App\Models\CommunityContribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    /**
     * @var list<string>
     */
    private const PERIODS = ['weekly', 'monthly', 'yearly', 'all'];

    public function index(Request $request): View
    {
        $period = $this->resolvePeriod($request->query('period', 'monthly'));

        $communities = Community::query()
            ->orderBy('name')
            ->get()
            ->map(function (Community $community) {
                $scores = $community->scores();

                return [
                    'id' => $community->id,
                    'slug' => $community->slug,
                    'name' => $community->name,
                    'description' => $community->description,
                    'flag_emoji' => $community->flag_emoji,
                    'scores' => $scores,
                    'business_count' => $community->businessContributionsCount(),
                    'contributions_count' => $community->contributionsCount(),
                ];
            })
            ->sortByDesc(fn (array $row) => $row['scores'][$period])
            ->values();

        $communitiesData = $communities->map(fn (array $row) => [
            'id' => $row['id'],
            'slug' => $row['slug'],
            'name' => $row['name'],
            'description' => $row['description'],
            'flag_emoji' => $row['flag_emoji'],
        ])->values();

        return view('Admin.topluluklar', [
            'communities' => $communities,
            'communitiesData' => $communitiesData,
            'period' => $period,
            'stats' => [
                'community_count' => Community::count(),
                'contributions_count' => CommunityContribution::count(),
                'monthly_points' => (int) CommunityContribution::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('points'),
                'all_time_points' => (int) CommunityContribution::query()->sum('points'),
            ],
        ]);
    }

    public function store(CommunityRequest $request): RedirectResponse
    {
        Community::query()->create($request->validated());

        return redirect()
            ->route('admin.topluluklar', ['period' => $request->input('redirect_period', 'monthly')])
            ->with('success', 'Topluluk eklendi.');
    }

    public function update(CommunityRequest $request, Community $community): RedirectResponse
    {
        $community->update($request->validated());

        return redirect()
            ->route('admin.topluluklar', ['period' => $request->input('redirect_period', 'monthly')])
            ->with('success', 'Topluluk güncellendi.');
    }

    public function destroy(Request $request, Community $community): RedirectResponse
    {
        $community->delete();

        return redirect()
            ->route('admin.topluluklar', ['period' => $request->input('redirect_period', 'monthly')])
            ->with('success', 'Topluluk silindi.');
    }

    private function resolvePeriod(?string $period): string
    {
        return in_array($period, self::PERIODS, true) ? $period : 'monthly';
    }
}
