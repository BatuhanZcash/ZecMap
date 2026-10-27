<?php

namespace App\Http\Controllers;

use App\Models\AcceptZecSite;
use Illuminate\View\View;

class AcceptZecController extends Controller
{
    public function index(): View
    {
        $sites = AcceptZecSite::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('accept-zec', [
            'sites' => $sites,
        ]);
    }
}
