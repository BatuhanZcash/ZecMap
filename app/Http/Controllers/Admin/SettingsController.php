<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $donationSetting = SiteSetting::query()
            ->where('key', SiteSetting::KEY_DONATION_ADDRESS)
            ->first();

        return view('Admin.settings', [
            'donationAddress' => $donationSetting?->value ?? config('zecmap.donation_address'),
            'donationUpdatedAt' => $donationSetting?->updated_at,
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        SiteSetting::setValue(
            SiteSetting::KEY_DONATION_ADDRESS,
            trim($request->validated('donation_address')),
        );

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Ayarlar kaydedildi.');
    }
}
