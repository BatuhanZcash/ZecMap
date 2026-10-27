<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    /**
     * Public "Zcash adoption" dashboard — how many businesses, cities and
     * countries accept Zcash, and how that has grown over time.
     * All figures are computed live from active business listings.
     */
    public function index(): View
    {
        $base = fn () => Business::query()->where('status', Business::STATUS_ACTIVE);

        $total = $base()->count();

        $cities = $base()
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->count('city');

        $countries = $base()
            ->whereNotNull('country_code')->where('country_code', '!=', '')
            ->distinct()->count('country_code');

        $categoryCount = $base()
            ->whereNotNull('category_id')
            ->distinct()->count('category_id');

        $verified = $base()->where('verified', 1)->count();

        // Businesses added in the last 30 days (momentum).
        $last30 = $base()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Top countries by number of Zcash-accepting businesses.
        $topCountries = $base()
            ->select('country_code', DB::raw('COUNT(*) as c'))
            ->whereNotNull('country_code')->where('country_code', '!=', '')
            ->groupBy('country_code')
            ->orderByDesc('c')
            ->limit(12)
            ->get();

        // Top categories.
        $topCategories = $base()
            ->join('categories', 'categories.id', '=', 'businesses.category_id')
            ->select('categories.name as name', DB::raw('COUNT(*) as c'))
            ->groupBy('categories.name')
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        // Monthly growth -> cumulative adoption curve.
        // Wrapped defensively: if the SQL date function is unavailable the
        // page still renders, just without the chart.
        $series = [];
        try {
            $monthly = $base()
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('COUNT(*) as c'))
                ->whereNotNull('created_at')
                ->groupBy('ym')
                ->orderBy('ym')
                ->get();

            $running = 0;
            foreach ($monthly as $row) {
                $running += (int) $row->c;
                $series[] = ['ym' => $row->ym, 'total' => $running];
            }
            // Keep the chart readable: last 24 data points.
            if (count($series) > 24) {
                $series = array_slice($series, -24);
            }
        } catch (\Throwable $e) {
            $series = [];
        }

        // Real business coordinates — these dots draw the world map itself.
        $points = $base()
            ->whereNotNull('lat')->whereNotNull('lng')
            ->inRandomOrder()
            ->limit(700)
            ->get(['lat', 'lng']);

        // Country nodes: where the glowing Zcash markers sit (real centroids).
        $nodes = $base()
            ->select(
                'country_code',
                DB::raw('COUNT(*) as c'),
                DB::raw('AVG(lat) as alat'),
                DB::raw('AVG(lng) as alng')
            )
            ->whereNotNull('country_code')->where('country_code', '!=', '')
            ->whereNotNull('lat')->whereNotNull('lng')
            ->groupBy('country_code')
            ->orderByDesc('c')
            ->limit(50)
            ->get();

        return view('adoption', [
            'total' => $total,
            'cities' => $cities,
            'countries' => $countries,
            'categoryCount' => $categoryCount,
            'verified' => $verified,
            'last30' => $last30,
            'topCountries' => $topCountries,
            'topCategories' => $topCategories,
            'series' => $series,
            'points' => $points,
            'nodes' => $nodes,
        ]);
    }
}
