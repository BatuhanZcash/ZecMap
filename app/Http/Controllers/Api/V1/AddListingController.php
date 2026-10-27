<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateBusinessListing;
use App\Http\Controllers\AddressSuggestController;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddListingRequest;
use App\Models\Business;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityContribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddListingController extends Controller
{
    public function form(): JsonResponse
    {
        return response()->json([
            'data' => [
                'categories' => Category::query()
                    ->orderBy('name')
                    ->get(['id', 'slug', 'name']),
                'communities' => Community::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'flag_emoji']),
                'community_points' => CommunityContribution::POINTS_BUSINESS_COMMUNITY,
                'payment_providers' => [
                    ['value' => Business::PAYMENT_ZCASH, 'label' => 'Zcash'],
                    ['value' => Business::PAYMENT_FLEXA, 'label' => 'Flexa'],
                ],
            ],
        ]);
    }

    public function store(AddListingRequest $request, CreateBusinessListing $createBusinessListing): JsonResponse
    {
        $validated = $request->validated();
        $business = $createBusinessListing->execute(
            $validated,
            $request->user()?->id,
            $request->file('logo'),
        );

        $communityId = $validated['community_id'] ?? null;
        $message = $communityId !== null
            ? __('addBusiness.successWithCommunity', [
                'points' => CommunityContribution::POINTS_BUSINESS_COMMUNITY,
            ])
            : __('addBusiness.successSubmitted');

        return response()->json([
            'message' => $message,
            'data' => [
                'id' => $business->id,
                'slug' => $business->slug,
            ],
        ], 201);
    }

    public function addressSuggest(Request $request, AddressSuggestController $addressSuggest): JsonResponse
    {
        return $addressSuggest($request);
    }
}
