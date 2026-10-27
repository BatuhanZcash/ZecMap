<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SavedBusinessController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()
                ->route('auth')
                ->with('error', __('saved.loginRequired'));
        }

        $businesses = auth()->user()
            ->savedBusinesses()
            ->with('category:id,name,slug')
            ->where('status', Business::STATUS_ACTIVE)
            ->get();

        return view('saved-businesses', [
            'businesses' => $businesses,
        ]);
    }

    public function slugs(): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['slugs' => []]);
        }

        return response()->json([
            'slugs' => auth()->user()
                ->savedBusinesses()
                ->where('status', Business::STATUS_ACTIVE)
                ->pluck('slug')
                ->values(),
        ]);
    }

    public function toggle(Business $business): JsonResponse
    {
        abort_unless(auth()->check(), 401);
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
            'saved' => $saved,
            'slug' => $business->slug,
            'slugs' => $user->savedBusinesses()
                ->where('status', Business::STATUS_ACTIVE)
                ->pluck('slug')
                ->values(),
        ]);
    }
}
