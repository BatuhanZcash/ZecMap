<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupporterRequest;
use App\Models\Supporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupporterController extends Controller
{
    public function index(): View
    {
        $supporters = Supporter::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $supportersData = $supporters->map(fn (Supporter $supporter) => [
            'id' => $supporter->id,
            'name' => $supporter->name,
            'description' => $supporter->description,
            'link_label' => $supporter->link_label,
            'link_url' => $supporter->link_url,
            'icon_url' => $supporter->iconDisplayUrl(),
            'sort_order' => $supporter->sort_order,
            'status' => $supporter->status,
            'status_label' => $supporter->statusLabel(),
        ])->values();

        return view('Admin.supporters', [
            'supporters' => $supporters,
            'supportersData' => $supportersData,
        ]);
    }

    public function store(SupporterRequest $request): RedirectResponse
    {
        Supporter::query()->create($this->attributesFromRequest($request));

        return redirect()
            ->route('admin.supporters')
            ->with('success', 'Destekleyen eklendi.');
    }

    public function update(SupporterRequest $request, Supporter $supporter): RedirectResponse
    {
        $supporter->update($this->attributesFromRequest($request, $supporter));

        return redirect()
            ->route('admin.supporters')
            ->with('success', 'Destekleyen güncellendi.');
    }

    public function destroy(Supporter $supporter): RedirectResponse
    {
        $supporter->delete();

        return redirect()
            ->route('admin.supporters')
            ->with('success', 'Destekleyen silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromRequest(SupporterRequest $request, ?Supporter $supporter = null): array
    {
        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'link_label' => $validated['link_label'],
            'link_url' => $validated['link_url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'status' => $validated['status'],
        ];

        if ($request->boolean('remove_icon')) {
            $attributes['icon_url'] = null;
        } elseif ($request->hasFile('icon')) {
            $directory = public_path('assets/images/supporter-icons');
            File::ensureDirectoryExists($directory);

            $file = $request->file('icon');
            $filename = Str::slug($validated['name']).'-'.time().'.'.$file->guessExtension();
            $file->move($directory, $filename);
            $attributes['icon_url'] = 'assets/images/supporter-icons/'.$filename;
        }

        return $attributes;
    }
}
