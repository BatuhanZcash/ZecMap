<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get();

        return view('blog', [
            'posts' => $posts,
        ]);
    }

    public function show(BlogPost $blog): View
    {
        abort_unless(
            $blog->isPublished()
            && $blog->published_at !== null
            && $blog->published_at->lte(now()),
            404,
        );

        return view('blog-detail', [
            'post' => $blog,
        ]);
    }
}
