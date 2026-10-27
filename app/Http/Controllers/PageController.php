<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsDashboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function analytics(Request $request, AnalyticsDashboard $dashboard): View
    {
        $periodDays = (int) $request->query('period', 30);
        $data = $dashboard->get($periodDays);
        $directory = $data['directory'];

        return view('pages.analytics', [
            'periodDays' => $data['periodDays'],
            'pageViews' => $data['pageViews'],
            'sessions' => $data['sessions'],
            'businessViews' => $data['businessViews'],
            'chartGeometry' => $data['chartGeometry'],
            'devices' => $data['devices'],
            'topCountries' => $data['topCountries'],
            'countryTotal' => $data['countryTotal'],
            'activeBusinesses' => $directory['activeBusinesses'],
            'verifiedBusinesses' => $directory['verifiedBusinesses'],
            'categoryCount' => $directory['categoryCount'],
            'reviewCount' => $directory['reviewCount'],
            'topCategories' => $directory['topCategories'],
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
