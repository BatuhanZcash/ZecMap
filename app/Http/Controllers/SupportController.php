<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Supporter;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        $supporters = Supporter::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('support', [
            'supporters' => $supporters,
            'donationAddress' => SiteSetting::getValue(
                SiteSetting::KEY_DONATION_ADDRESS,
                config('zecmap.donation_address'),
            ),
        ]);
    }
}
