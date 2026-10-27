<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\BlogPostSummaryResource;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get();

        return response()
            ->json([
                'data' => BlogPostSummaryResource::collection($posts),
                'meta' => ['count' => $posts->count()],
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()
            ->json(['data' => new BlogPostResource($post)])
            ->header('Cache-Control', 'public, max-age=120');
    }
}
