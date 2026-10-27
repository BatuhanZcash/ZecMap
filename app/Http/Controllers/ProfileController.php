<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\BusinessVerification;
use App\Models\ZecMoonSpawn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        if (! auth()->check()) {
            return view('profile', [
                'guest' => true,
            ]);
        }

        $user = auth()->user();

        $verifications = BusinessVerification::query()
            ->with('business')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $businesses = Business::query()
            ->with('category')
            ->where('owner_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $reviews = BusinessReview::query()
            ->with(['business.category', 'community'])
            ->approved()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();

        $savedCount = $user->savedBusinesses()
            ->where('status', Business::STATUS_ACTIVE)
            ->count();

        return view('profile', [
            'guest' => false,
            'user' => $user,
            'username' => $user->username,
            'initial' => $user->initials(),
            'avatarUrl' => $user->avatarDisplayUrl(),
            'verificationCount' => BusinessVerification::query()->where('user_id', $user->id)->count(),
            'reviewCount' => BusinessReview::query()->approved()->where('user_id', $user->id)->count(),
            'zecmoonCount' => ZecMoonSpawn::query()->where('captured_by', $user->id)->count(),
            'savedCount' => $savedCount,
            'verifications' => $verifications,
            'businesses' => $businesses,
            'reviews' => $reviews,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
        ], [
            'current_password.required' => 'Current password is required.',
            'current_password.current_password' => 'Current password is incorrect.',
            'password.required' => 'New password is required.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'New passwords do not match.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('profile')
                ->withErrors($validator, 'password');
        }

        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()
            ->route('profile')
            ->with('password_status', 'Password updated successfully.');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $attributes = [
            'name' => trim($validated['name']),
            'bio' => isset($validated['bio']) ? trim($validated['bio']) : null,
            'payout_address' => isset($validated['payout_address']) && $validated['payout_address'] !== ''
                ? trim($validated['payout_address'])
                : null,
        ];

        if ($request->boolean('remove_avatar')) {
            $attributes['avatar_url'] = null;
        } elseif ($request->hasFile('avatar')) {
            $directory = public_path('assets/images/avatars');
            File::ensureDirectoryExists($directory);

            $file = $request->file('avatar');
            $filename = Str::slug($user->username).'-'.time().'.'.$file->guessExtension();
            $file->move($directory, $filename);
            $attributes['avatar_url'] = 'assets/images/avatars/'.$filename;
        }

        $user->update($attributes);

        return redirect()
            ->route('profile')
            ->with('success', 'Profile updated!');
    }
}
