<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileReviewResource;
use App\Http\Resources\ProfileVerificationResource;
use App\Http\Resources\UserResource;
use App\Models\BusinessReview;
use App\Models\BusinessVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $reviews = BusinessReview::query()
            ->with(['business.category', 'user:id,name,username,email', 'community:id,name,slug,flag_emoji'])
            ->approved()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();

        $verifications = BusinessVerification::query()
            ->with('business')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'user' => (new UserResource($user))->resolve(),
                'stats' => [
                    'verification_count' => BusinessVerification::query()->where('user_id', $user->id)->count(),
                    'review_count' => BusinessReview::query()->approved()->where('user_id', $user->id)->count(),
                ],
                'reviews' => ProfileReviewResource::collection($reviews)->resolve(),
                'verifications' => ProfileVerificationResource::collection($verifications)->resolve(),
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $attributes = [
            'name' => trim($validated['name']),
            'bio' => isset($validated['bio']) ? trim($validated['bio']) : null,
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
        $user->refresh();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => (new UserResource($user))->resolve(),
            ],
        ]);
    }
}
