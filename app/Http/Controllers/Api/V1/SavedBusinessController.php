<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;

class SavedBusinessController extends Controller
{
    public function index(): JsonResponse
    {
        $businesses = auth()->user()
            ->savedBusinesses()
            ->with('category:id,name,slug')
            ->where('status', Business::STATUS_ACTIVE)
            ->get()
            ->map(fn (Business $business) => [
                'id' => $business->id,
                'slug' => $business->slug,
                'name' => $business->name,
                'logo_url' => $business->logoDisplayUrl(),
                'address' => $business->address,
                'city' => $business->city,
                'verified' => $business->verified,
                'category' => $business->category?->name,
            ])
            ->values();

        return response()->json(['data' => $businesses]);
    }

    public function slugs(): JsonResponse
    {
        return response()->json([
            'data' => auth()->user()
                ->savedBusinesses()
                ->where('status', Business::STATUS_ACTIVE)
                ->pluck('slug')
                ->values(),
        ]);
    }

    public function toggle(Business $business): JsonResponse
    {
        abort_unless($business->isActive(), 404);

        $user = auth()->user();
        $attached = $user->savedBusinesses()
            ->where('business_id', $business->id)
            ->exists();

        if ($attached) {
            $user->savedBusinesses()->detach($business->id);
            $saved = false;
        } else {
            $user->savedBusinesses()->attach($business->id);
            $saved = true;
        }

        return response()->json([
            'data' => [
                'saved' => $saved,
                'slug' => $business->slug,
                'slugs' => $user->savedBusinesses()
                    ->where('status', Business::STATUS_ACTIVE)
                    ->pluck('slug')
                    ->values(),
            ],
        ]);
    }
}
