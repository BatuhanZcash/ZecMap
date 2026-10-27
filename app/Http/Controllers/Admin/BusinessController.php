<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessImportRequest;
use App\Http\Requests\Admin\BusinessRequest;
use App\Models\Business;
use App\Models\BusinessJsonImport;
use App\Models\Category;
use App\Services\BusinessJsonImportCleanup;
use App\Services\BusinessJsonImporter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'all');
        $category = $request->query('category', 'all');

        $businesses = $this->filteredBusinessQuery($request)
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $businessesData = collect($businesses->items())->map(fn (Business $business) => [
            'id' => $business->id,
            'name' => $business->name,
            'category_id' => $business->category_id,
            'description' => $business->description,
            'address' => $business->address,
            'lat' => $business->lat,
            'lng' => $business->lng,
            'verified' => $business->verified,
            'logo_url' => $business->logoDisplayUrl(),
            'status' => $business->status,
            'website' => $business->website,
            'phone' => $business->phone,
            'zcash_address' => $business->zcash_address,
            'payment_provider' => $business->payment_provider ?? Business::PAYMENT_ZCASH,
            'social_links' => $business->social_links ?? [],
            'sort_order' => $business->sort_order ?? 0,
            'badge_key' => $business->badge_key,
            'badge_label' => $business->badge_label,
        ])->values();

        return view('Admin.isletmeler', [
            'businesses' => $businesses,
            'categories' => Category::orderBy('name')->get(),
            'businessesData' => $businessesData,
            'search' => $search,
            'status' => $status,
            'category' => $category,
            'totalCount' => $businesses->total(),
        ]);
    }

    private function filteredBusinessQuery(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'all');
        $category = $request->query('category', 'all');

        return Business::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%')
                        ->orWhere('city', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when(
                in_array($status, [Business::STATUS_ACTIVE, Business::STATUS_INACTIVE, Business::STATUS_CLOSED], true),
                fn ($query) => $query->where('status', $status),
            )
            ->when(
                $category !== 'all' && $category !== '' && is_numeric($category),
                fn ($query) => $query->where('category_id', (int) $category),
            )
            ->orderBy('name');
    }

    public function store(BusinessRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = $this->generateUniqueSlug($validated['name']);

        $attributes = $this->attributesFromValidated($validated, [], $slug);

        if ($request->hasFile('logo')) {
            $attributes['logo_url'] = $this->storeLogo($request->file('logo'), $slug);
        }

        Business::query()->create($attributes);

        return redirect()
            ->route('admin.isletmeler', $this->redirectParams($request))
            ->with('success', 'İşletme eklendi.');
    }

    public function update(BusinessRequest $request, Business $business): RedirectResponse
    {
        $validated = $request->validated();
        $attributes = $this->attributesFromValidated(
            $validated,
            $business->social_links ?? [],
            $business->slug ?? $this->generateUniqueSlug($validated['name'], $business->id),
        );

        if ($request->boolean('remove_logo')) {
            $attributes['logo_url'] = null;
        } elseif ($request->hasFile('logo')) {
            $attributes['logo_url'] = $this->storeLogo(
                $request->file('logo'),
                $business->slug ?? $this->generateUniqueSlug($validated['name']),
            );
        }

        $business->update($attributes);

        return redirect()
            ->route('admin.isletmeler', $this->redirectParams($request))
            ->with('success', 'İşletme güncellendi.');
    }

    public function destroy(Request $request, Business $business): RedirectResponse
    {
        $business->delete();

        return redirect()
            ->route('admin.isletmeler', $this->redirectParams($request))
            ->with('success', 'İşletme silindi.');
    }

    public function import(BusinessImportRequest $request, BusinessJsonImporter $importer): RedirectResponse|JsonResponse
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        $contents = file_get_contents($request->file('json_file')->getRealPath());
        $decoded = json_decode($contents ?: '', true);
        $redirectParams = $this->redirectParams($request);
        $redirectUrl = route('admin.isletmeler', $redirectParams);

        if (! is_array($decoded)) {
            return $this->importResponse(
                $request,
                $redirectUrl,
                success: false,
                message: 'JSON dosyası geçerli bir dizi içermelidir.',
            );
        }

        $items = array_is_list($decoded) ? $decoded : [$decoded];

        $result = DB::transaction(function () use ($request, $importer, $items) {
            $import = BusinessJsonImport::query()->create([
                'original_filename' => $request->file('json_file')->getClientOriginalName(),
                'total_items' => count($items),
                'uploaded_by_user_id' => Auth::id(),
                'imported_at' => now(),
            ]);

            $result = $importer->import($items, $import->id);

            $import->update([
                'created_count' => $result['created'],
                'skipped_count' => $result['skipped'],
                'failed_count' => $result['failed'],
            ]);

            return $result;
        });

        if ($result['created'] === 0 && $result['skipped'] === 0 && $result['failed'] > 0) {
            return $this->importResponse(
                $request,
                $redirectUrl,
                success: false,
                message: $result['errors'][0] ?? 'İçe aktarma başarısız.',
            );
        }

        $message = sprintf('%d işletme eklendi.', $result['created']);

        if ($result['skipped'] > 0) {
            $message .= sprintf(' %d kayıt zaten mevcut (atlandı).', $result['skipped']);
        }

        if ($result['failed'] > 0) {
            $message .= sprintf(' %d kayıt geçersiz.', $result['failed']);
        }

        return $this->importResponse($request, $redirectUrl, success: true, message: $message, result: $result);
    }

    public function importHistory(): View
    {
        $imports = BusinessJsonImport::query()
            ->with('uploadedBy:id,name,email')
            ->withCount('businesses')
            ->orderByDesc('imported_at')
            ->paginate(20);

        return view('Admin.isletmeler-import-gecmisi', [
            'imports' => $imports,
        ]);
    }

    public function destroyImport(BusinessJsonImport $import, BusinessJsonImportCleanup $cleanup): RedirectResponse
    {
        $importId = $import->id;
        $deletedCount = $cleanup->deleteImport($import);

        return redirect()
            ->route('admin.isletmeler.import-history')
            ->with('success', sprintf(
                'İçe aktarma #%d silindi. %d işletme kaldırıldı.',
                $importId,
                $deletedCount,
            ));
    }

    /**
     * @param  array{created: int, skipped: int, failed: int, errors: list<string>}|null  $result
     */
    private function importResponse(
        Request $request,
        string $redirectUrl,
        bool $success,
        string $message,
        ?array $result = null,
    ): RedirectResponse|JsonResponse {
        if ($request->expectsJson()) {
            if (! $success) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            session()->flash('success', $message);

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => $redirectUrl,
                'result' => $result,
            ]);
        }

        if (! $success) {
            return redirect()
                ->to($redirectUrl)
                ->withErrors(['json_file' => $message]);
        }

        return redirect()
            ->to($redirectUrl)
            ->with('success', $message);
    }

    /**
     * @param  array<string, mixed>  $existingSocialLinks
     * @return array<string, mixed>
     */
    private function attributesFromValidated(array $validated, array $existingSocialLinks, string $slug): array
    {
        $socialLinks = $existingSocialLinks;
        $twitter = trim((string) ($validated['social_twitter'] ?? ''));

        if ($twitter !== '') {
            $socialLinks['twitter'] = ltrim($twitter, '@');
        } else {
            unset($socialLinks['twitter']);
        }

        return [
            'slug' => $slug,
            'name' => $validated['name'],
            'status' => $validated['status'],
            'category_id' => $validated['category_id'] ?? null,
            'verified' => $validated['verified'] ?? false,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'zcash_address' => $validated['zcash_address'] ?? null,
            'payment_provider' => $validated['payment_provider'],
            'social_links' => $socialLinks,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'badge_key' => $validated['badge_key'] ?? null,
            'badge_label' => ($validated['badge_key'] ?? null) === Business::BADGE_CUSTOM
                ? ($validated['badge_label'] ?? null)
                : null,
        ];
    }

    private function storeLogo(UploadedFile $file, string $slug): string
    {
        $directory = public_path('assets/images/business-logos');
        File::ensureDirectoryExists($directory);

        $filename = $slug.'-'.time().'.'.$file->guessExtension();
        $file->move($directory, $filename);

        return 'assets/images/business-logos/'.$filename;
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'business';
        }

        $slug = $base;
        $counter = 2;

        while (
            Business::query()
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @return array<string, string|null>
     */
    private function redirectParams(Request $request): array
    {
        $redirectStatus = $request->input('redirect_status', 'all');
        $redirectCategory = $request->input('redirect_category', 'all');
        $redirectPage = $request->input('redirect_page');

        return array_filter([
            'search' => $request->input('redirect_search'),
            'status' => $redirectStatus !== 'all' ? $redirectStatus : null,
            'category' => $redirectCategory !== 'all' && $redirectCategory !== '' ? $redirectCategory : null,
            'page' => is_numeric($redirectPage) && (int) $redirectPage > 1 ? (int) $redirectPage : null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
