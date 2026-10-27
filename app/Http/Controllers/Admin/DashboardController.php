<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\BusinessVerification;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeBusinessCount = Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->count();

        $pendingApplicationCount = Business::query()
            ->where('status', Business::STATUS_INACTIVE)
            ->count();

        $totalReviewCount = BusinessReview::query()->approved()->count();
        $pendingReviewCount = BusinessReview::query()->pending()->count();

        $verificationCount = BusinessVerification::query()->count();

        $categoryStats = Category::query()
            ->withCount([
                'businesses' => fn ($query) => $query->where('status', Business::STATUS_ACTIVE),
            ])
            ->orderByDesc('businesses_count')
            ->get()
            ->filter(fn (Category $category) => $category->businesses_count > 0)
            ->values()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => $category->businesses_count,
            ]);

        $recentApplications = Business::query()
            ->where('status', Business::STATUS_INACTIVE)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('Admin.dashboard', [
            'activeBusinessCount' => $activeBusinessCount,
            'pendingApplicationCount' => $pendingApplicationCount,
            'totalReviewCount' => $totalReviewCount,
            'pendingReviewCount' => $pendingReviewCount,
            'verificationCount' => $verificationCount,
            'categoryStats' => $categoryStats,
            'recentApplications' => $recentApplications,
            'chartMaxCount' => max($categoryStats->max('count') ?? 0, 1),
        ]);
    }
}
