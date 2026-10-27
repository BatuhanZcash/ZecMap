<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Community;
use App\Support\CountryResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CountryToolController extends Controller
{
    private function localQuery()
    {
        return Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->where('payment_provider', '!=', Business::PAYMENT_FLEXA)
                    ->orWhereNull('payment_provider');
            });
    }

    public function index(): View
    {
        $total = (clone $this->localQuery())->count();
        $withCode = (clone $this->localQuery())->whereNotNull('country_code')->count();
        $missing = (clone $this->localQuery())
            ->whereNull('country_code')
            ->whereNotNull('lat')->whereNotNull('lng')
            ->count();
        $noCoords = (clone $this->localQuery())
            ->whereNull('country_code')
            ->where(function ($q) {
                $q->whereNull('lat')->orWhereNull('lng');
            })->count();

        $countryCodes = (clone $this->localQuery())
            ->whereNotNull('country_code')
            ->distinct()->pluck('country_code');
        $missingCommunities = $countryCodes
            ->reject(fn ($cc) => Community::where('country_code', $cc)->exists())
            ->count();

        return view('Admin.ulke-araci', [
            'total' => $total,
            'withCode' => $withCode,
            'missing' => $missing,
            'noCoords' => $noCoords,
            'countryCount' => $countryCodes->count(),
            'missingCommunities' => $missingCommunities,
        ]);
    }

    public function run(CountryResolver $resolver): RedirectResponse
    {
        // 1) Assign country_code to local businesses that have coordinates but no code.
        $assigned = 0;
        (clone $this->localQuery())
            ->whereNull('country_code')
            ->whereNotNull('lat')->whereNotNull('lng')
            ->select('id', 'lat', 'lng')
            ->chunkById(300, function ($rows) use ($resolver, &$assigned) {
                foreach ($rows as $b) {
                    $cc = $resolver->resolve((float) $b->lat, (float) $b->lng);
                    if ($cc !== null) {
                        Business::whereKey($b->id)->update(['country_code' => $cc]);
                        $assigned++;
                    }
                }
            });

        // 2) Create a community for every country that now has a local business.
        $meta = $resolver->meta();
        $created = 0;
        $countryCodes = (clone $this->localQuery())
            ->whereNotNull('country_code')
            ->distinct()->pluck('country_code');

        foreach ($countryCodes as $cc) {
            if (Community::where('country_code', $cc)->exists()) {
                continue;
            }

            $m = $meta[$cc] ?? null;
            $name = $m['name'] ?? $cc;
            $slug = Str::slug($name) ?: strtolower($cc);
            // Avoid slug collisions with existing communities.
            $base = $slug;
            $i = 2;
            while (Community::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }

            Community::create([
                'slug' => $slug,
                'name' => $name,
                'description' => 'Zcash businesses and adopters in '.$name.'.',
                'flag_emoji' => $m['flag'] ?? null,
                'country_code' => $cc,
            ]);
            $created++;
        }

        return redirect()
            ->route('admin.ulke-araci')
            ->with('success', "Tamamlandi. $assigned isletme ulkesine atandi, $created yeni ulke toplulugu olusturuldu.");
    }
}
