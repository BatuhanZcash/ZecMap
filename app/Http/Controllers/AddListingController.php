<?php

namespace App\Http\Controllers;

use App\Actions\CreateBusinessListing;
use App\Http\Requests\AddListingRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityContribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddListingController extends Controller
{
    public function create(): View
    {
        return view('add-listing', [
            'categories' => Category::orderBy('name')->get(),
            'communities' => Community::orderBy('name')->get(),
        ]);
    }

    public function store(AddListingRequest $request, CreateBusinessListing $createBusinessListing): RedirectResponse
    {
        $validated = $request->validated();
        $createBusinessListing->execute(
            $validated,
            auth()->id(),
            $request->file('logo'),
        );

        $communityId = $validated['community_id'] ?? null;
        $successMessage = $communityId !== null
            ? __('addBusiness.successWithCommunity', [
                'points' => CommunityContribution::POINTS_BUSINESS_COMMUNITY,
            ])
            : __('addBusiness.successSubmitted');

        return redirect()
            ->route('add')
            ->with('success', $successMessage);
    }
}
