<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $posts = BlogPost::query()
            ->when(
                in_array($status, [BlogPost::STATUS_DRAFT, BlogPost::STATUS_PUBLISHED], true),
                fn ($query) => $query->where('status', $status),
            )
            ->orderByDesc('created_at')
            ->get();

        return view('Admin.blogs.list', [
            'posts' => $posts,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('Admin.blogs.creat');
    }

    public function edit(BlogPost $blog): View
    {
        return view('Admin.blogs.creat', [
            'post' => $blog,
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $attributes = $this->attributesFromRequest($request);

        if ($request->hasFile('featured_image')) {
            $attributes['featured_image'] = $this->storeFeaturedImage(
                $request->file('featured_image'),
                $attributes['slug'],
            );
        }

        BlogPost::create($attributes);

        return redirect()
            ->route('admin.blogs.list')
            ->with('success', 'Blog yazısı oluşturuldu.');
    }

    public function update(BlogPostRequest $request, BlogPost $blog): RedirectResponse
    {
        $attributes = $this->attributesFromRequest($request, $blog);

        if ($request->boolean('remove_featured_image')) {
            $this->deleteFeaturedImage($blog->featured_image);
            $attributes['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            $this->deleteFeaturedImage($blog->featured_image);
            $attributes['featured_image'] = $this->storeFeaturedImage(
                $request->file('featured_image'),
                $attributes['slug'],
            );
        }

        $blog->update($attributes);

        return redirect()
            ->route('admin.blogs.list')
            ->with('success', 'Blog yazısı güncellendi.');
    }

    public function unpublish(Request $request, BlogPost $blog): RedirectResponse
    {
        if (! $blog->isPublished()) {
            return $this->redirectToList($request, 'Bu yazı zaten yayından kaldırılmış.');
        }

        $blog->update([
            'status' => BlogPost::STATUS_DRAFT,
            'published_at' => null,
        ]);

        return $this->redirectToList($request, 'Yazı yayından kaldırıldı.');
    }

    public function destroy(Request $request, BlogPost $blog): RedirectResponse
    {
        $this->deleteFeaturedImage($blog->featured_image);
        $blog->delete();

        return $this->redirectToList($request, 'Blog yazısı silindi.');
    }

    private function redirectToList(Request $request, string $message): RedirectResponse
    {
        $redirectStatus = $request->input('redirect_status');

        return redirect()
            ->route('admin.blogs.list', array_filter([
                'status' => is_string($redirectStatus) && $redirectStatus !== '' ? $redirectStatus : null,
            ]))
            ->with('success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromRequest(BlogPostRequest $request, ?BlogPost $blog = null): array
    {
        $validated = $request->validated();
        $user = Auth::user();

        $attributes = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'status' => $validated['status'],
            'author_id' => $user?->id,
            'author_name' => $user?->name ?? 'Admin',
        ];

        if ($validated['status'] === BlogPost::STATUS_PUBLISHED) {
            $attributes['published_at'] = $blog?->published_at ?? now();
        } else {
            $attributes['published_at'] = null;
        }

        return $attributes;
    }

    private function storeFeaturedImage(UploadedFile $file, string $slug): string
    {
        $directory = public_path('assets/images/blog-images');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true)) {
            $extension = match ($file->getMimeType()) {
                'image/jpeg', 'image/pjpeg' => 'jpg',
                'image/png', 'image/x-png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/bmp' => 'bmp',
                default => 'jpg',
            };
        }

        $filename = $slug.'-'.time().'.'.$extension;
        $file->move($directory, $filename);

        return 'assets/images/blog-images/'.$filename;
    }

    private function deleteFeaturedImage(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $fullPath = public_path(ltrim($path, '/'));

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
