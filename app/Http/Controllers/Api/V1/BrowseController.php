<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrowseBusinessResource;
use App\Http\Resources\BrowseCategoryResource;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    private const PER_PAGE = 24;

    public function categories(): JsonResponse
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

        return response()
            ->json([
                'data' => BrowseCategoryResource::collection($categories)->resolve(),
                'meta' => ['count' => $categories->count()],
            ])
            ->header('Cache-Control', 'public, max-age=120');
    }

    public function businesses(Request $request): JsonResponse
    {
        $perPage = min(48, max(1, (int) $request->query('per_page', self::PER_PAGE)));
        $paginator = $this->businessQuery($request)->paginate($perPage);

        return response()
            ->json([
                'data' => BrowseBusinessResource::collection($paginator->items())->resolve(),
                'meta' => [
                    'total' => $paginator->total(),
                    'has_more' => $paginator->hasMorePages(),
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
                ],
            ])
            ->header('Cache-Control', 'public, max-age=60');
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

    private function applyBrowseVisibility($query)
    {
        return $query->where(function ($builder) {
            $builder->whereNull('payment_provider')
                ->orWhere('payment_provider', '!=', Business::PAYMENT_FLEXA);
        });
    }
}
