<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessUpdateRequest;
use App\Models\Business;
use App\Models\BusinessUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class BusinessUpdateRequestController extends Controller
{
    public function store(StoreBusinessUpdateRequest $request, Business $business): RedirectResponse
    {
        // Pages exist for active and pending (inactive) businesses, not for rejected/closed ones.
        abort_if($business->status === Business::STATUS_CLOSED, 404);

        // Bulk imported (Flexa) listings are not individually managed by owners.
        abort_if($business->payment_provider === Business::PAYMENT_FLEXA, 404);

        $validated = $request->validated();
        $userId = (int) auth()->id();

        $existingPending = BusinessUpdateRequest::query()
            ->where('business_id', $business->id)
            ->where('user_id', $userId)
            ->where('status', BusinessUpdateRequest::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return redirect()
                ->route('business.show', $business)
                ->with('error', 'You already have an update request awaiting review for this business.');
        }

        $changes = [];

        foreach (['name', 'description', 'phone', 'address', 'city', 'hours'] as $field) {
            $value = trim((string) ($validated[$field] ?? ''));

            if ($value !== '') {
                $changes[$field] = $value;
            }
        }

        $website = trim((string) ($validated['website'] ?? ''));

        if ($website !== '') {
            $changes['website'] = $this->normalizeUrl($website);
        }

        $socials = [];

        foreach (['twitter', 'instagram', 'telegram', 'facebook', 'email'] as $key) {
            $value = trim((string) ($validated['social_'.$key] ?? ''));

            if ($value !== '') {
                $socials[$key] = $value;
            }
        }

        if ($socials !== []) {
            $changes['social_links'] = $socials;
        }

        $proposedLogo = null;

        if ($request->hasFile('logo')) {
            $proposedLogo = $this->storeProposedLogo($request->file('logo'), $business, $userId);
        }

        $wantsWallet = (bool) ($validated['wants_wallet_change'] ?? false);
        $message = trim((string) ($validated['message'] ?? ''));

        if ($changes === [] && $proposedLogo === null && ! $wantsWallet && $message === '') {
            return redirect()
                ->route('business.show', $business)
                ->with('error', 'Please fill in at least one field or attach a logo before sending an update request.');
        }

        BusinessUpdateRequest::query()->create([
            'business_id' => $business->id,
            'user_id' => $userId,
            'changes' => $changes === [] ? null : $changes,
            'proposed_logo_url' => $proposedLogo,
            'wants_wallet_change' => $wantsWallet,
            'message' => $message === '' ? null : $message,
            'status' => BusinessUpdateRequest::STATUS_PENDING,
        ]);

        $successMessage = $wantsWallet
            ? 'Thanks. Your update request was sent for review. To change the payment address, please also reach us from the business\'s listed email, website or Telegram so we can confirm you are the owner.'
            : 'Thanks. Your update request has been sent and is awaiting review.';

        return redirect()
            ->route('business.show', $business)
            ->with('success', $successMessage);
    }

    private function storeProposedLogo(UploadedFile $file, Business $business, int $userId): string
    {
        $directory = public_path('assets/images/business-updates');
        File::ensureDirectoryExists($directory);

        $filename = ($business->slug ?? 'business').'-'.$userId.'-'.time().'.'.$file->guessExtension();
        $file->move($directory, $filename);

        return 'assets/images/business-updates/'.$filename;
    }

    private function normalizeUrl(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return 'https://'.$url;
    }
}
