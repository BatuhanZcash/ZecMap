<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request): View
    {
        $pins = $this->mapBusinessQuery()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(['id', 'slug', 'name', 'lat', 'lng', 'logo_url', 'payment_provider'])
            ->orderBy('name')
            ->get()
            ->map(fn (Business $business) => $this->formatMapPin($business))
            ->values();

        $center = [10, 25];
        $zoom = 1.8;
        $focusBusinessId = null;

        $focusSlug = trim((string) $request->query('business', ''));
        if ($focusSlug !== '') {
            $focusedPin = $pins->firstWhere('slug', $focusSlug);
            if ($focusedPin) {
                $center = [$focusedPin['lng'], $focusedPin['lat']];
                $zoom = 14;
                $focusBusinessId = $focusedPin['id'];
            }
        } elseif ($pins->isNotEmpty()) {
            $center = [
                $pins->avg('lng'),
                $pins->avg('lat'),
            ];
            $zoom = $pins->count() === 1 ? 10 : 2.5;
        }

        return view('map', [
            'mapPins' => $pins,
            'mapCenter' => $center,
            'mapZoom' => $zoom,
            'focusBusinessId' => $focusBusinessId,
        ]);
    }

    public function business(Business $business): JsonResponse
    {
        abort_unless($business->isActive(), 404);
        abort_unless($business->lat !== null && $business->lng !== null, 404);

        $business->load('category:id,name');

        return response()->json([
            'business' => $this->formatBusiness($business),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['businesses' => []]);
        }

        $businesses = $this->mapBusinessQuery()
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', '%'.$query.'%')
                    ->orWhere('slug', 'like', '%'.$query.'%')
                    ->orWhere('city', 'like', '%'.$query.'%')
                    ->orWhere('address', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$query.'%'));
            })
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (Business $business) => $this->formatBusiness($business))
            ->values();

        return response()->json(['businesses' => $businesses]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Business>
     */
    private function mapBusinessQuery()
    {
        return Business::query()
            ->where('status', Business::STATUS_ACTIVE);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMapPin(Business $business): array
    {
        return [
            'id' => $business->id,
            'slug' => $business->slug,
            'name' => $business->name,
            'lat' => $business->lat,
            'lng' => $business->lng,
            'logo_url' => $business->logoDisplayUrl(),
            'payment_provider' => $business->payment_provider ?? Business::PAYMENT_ZCASH,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatBusiness(Business $business): array
    {
        return [
            'id' => $business->id,
            'slug' => $business->slug,
            'detail_url' => route('business.show', $business->slug),
            'name' => $business->name,
            'lat' => $business->lat,
            'lng' => $business->lng,
            'city' => $business->city,
            'category' => $business->category?->name,
            'verified' => $business->verified,
            'logo_url' => $business->logoDisplayUrl(),
            'description' => $business->description,
            'address' => $business->address,
            'phone' => $business->phone,
            'website' => $business->website,
            'payment_provider' => $business->payment_provider ?? Business::PAYMENT_ZCASH,
        ];
    }
}
