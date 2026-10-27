<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcceptZecSiteRequest;
use App\Models\AcceptZecSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AcceptZecSiteController extends Controller
{
    public function index(): View
    {
        $sites = AcceptZecSite::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sitesData = $sites->map(fn (AcceptZecSite $site) => [
            'id' => $site->id,
            'name' => $site->name,
            'description' => $site->description,
            'website_url' => $site->website_url,
            'website_host' => $site->websiteDisplayHost(),
            'logo_url' => $site->logoDisplayUrl(),
            'sort_order' => $site->sort_order,
            'status' => $site->status,
            'status_label' => $site->statusLabel(),
        ])->values();

        return view('Admin.accept-zec', [
            'sites' => $sites,
            'sitesData' => $sitesData,
        ]);
    }

    public function store(AcceptZecSiteRequest $request): RedirectResponse
    {
        AcceptZecSite::query()->create($this->attributesFromRequest($request));

        return redirect()
            ->route('admin.accept-zec')
            ->with('success', 'Site eklendi.');
    }

    public function update(AcceptZecSiteRequest $request, AcceptZecSite $acceptZecSite): RedirectResponse
    {
        $acceptZecSite->update($this->attributesFromRequest($request, $acceptZecSite));

        return redirect()
            ->route('admin.accept-zec')
            ->with('success', 'Site güncellendi.');
    }

    public function destroy(AcceptZecSite $acceptZecSite): RedirectResponse
    {
        $acceptZecSite->delete();

        return redirect()
            ->route('admin.accept-zec')
            ->with('success', 'Site silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromRequest(AcceptZecSiteRequest $request, ?AcceptZecSite $site = null): array
    {
        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'website_url' => $validated['website_url'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'status' => $validated['status'],
        ];

        if ($request->boolean('remove_logo')) {
            $attributes['logo_url'] = null;
        } elseif ($request->hasFile('logo')) {
            $directory = public_path('assets/images/accept-zec-logos');
            File::ensureDirectoryExists($directory);

            $file = $request->file('logo');
            $filename = Str::slug($validated['name']).'-'.time().'.'.$file->guessExtension();
            $file->move($directory, $filename);
            $attributes['logo_url'] = 'assets/images/accept-zec-logos/'.$filename;
        }

        return $attributes;
    }
}
