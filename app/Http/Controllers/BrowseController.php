<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseController extends Controller
{
    private const PER_PAGE = 24;

    public function index(): View
    {
        $categories = Category::query()
            ->withCount([
                'businesses as active_businesses_count' => function ($query) {
                    $this->applyBrowseVisibility(
                        $query->where('status', Business::STATUS_ACTIVE),
                    );
                },
            ])
            ->having('active_businesses_count', '>', 0)
            ->orderBy('name')
            ->get();

        $paginator = $this->businessQuery(new Request)->paginate(self::PER_PAGE);
        $businesses = collect($paginator->items())
            ->map(fn (Business $business) => $this->formatBusiness($business));

        return view('browse', [
            'categories' => $categories,
            'businesses' => $businesses,
            'totalCount' => $paginator->total(),
            'hasMore' => $paginator->hasMorePages(),
            'nextPage' => $paginator->hasMorePages() ? 2 : null,
        ]);
    }

    public function businesses(Request $request): JsonResponse
    {
        $paginator = $this->businessQuery($request)->paginate(self::PER_PAGE);

        $html = '';
        foreach ($paginator->items() as $business) {
            $html .= view('browse.partials.card', [
                'business' => $this->formatBusiness($business),
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
            'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
        ]);
    }

    private function businessQuery(Request $request)
    {
        $query = $this->applyBrowseVisibility(
            Business::query()
                ->with('category:id,name,slug')
                ->where('status', Business::STATUS_ACTIVE),
        );

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($builder) use ($like) {
                $builder->where('name', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', $like));
            });
        }

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
        }

        $sort = (string) $request->query('sort', 'newest');

        return $query->orderedForBrowse($sort);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatBusiness(Business $business): array
    {
        $socialLinks = is_array($business->social_links) ? $business->social_links : [];
        $twitterUrl = $socialLinks['twitter'] ?? $socialLinks['x'] ?? null;

        $website = $business->website;
        if ($website && ! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'https://'.$website;
        }

        if ($twitterUrl && ! str_starts_with($twitterUrl, 'http://') && ! str_starts_with($twitterUrl, 'https://')) {
            $twitterUrl = 'https://'.$twitterUrl;
        }

        $searchText = strtolower(trim(implode(' ', array_filter([
            $business->name,
            $business->category?->name,
            $business->city,
            $business->address,
            $business->description,
        ]))));

        return [
            'id' => $business->id,
            'slug' => $business->slug,
            'name' => $business->name,
            'category_name' => $business->category?->name,
            'category_slug' => $business->category?->slug,
            'cover_class' => $this->coverClassForCategory($business->category?->slug),
            'logo_url' => $business->logoDisplayUrl(),
            'verified' => $business->verified,
            'website' => $website,
            'twitter_url' => $twitterUrl,
            'city' => $business->city,
            'address' => $business->address,
            'is_new' => $business->created_at?->greaterThan(now()->subDays(30)) ?? false,
            'created_at' => $business->created_at?->timestamp ?? 0,
            'badge' => $business->browseBadge(),
            'detail_url' => route('business.show', $business->slug),
            'search_text' => $searchText,
        ];
    }

    private function applyBrowseVisibility($query)
    {
        return $query->where(function ($builder) {
            $builder->whereNull('payment_provider')
                ->orWhere('payment_provider', '!=', Business::PAYMENT_FLEXA);
        });
    }

    private function coverClassForCategory(?string $slug): string
    {
        return match ($slug) {
            'crypto-wallets' => 'wallets',
            'food-drink' => 'local-merchants',
            'retail' => 'online-shopping',
            'services' => 'vpn-services',
            'travel' => 'local-merchants',
            default => 'default',
        };
    }
}
