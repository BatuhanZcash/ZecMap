<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('businesses')
            ->orderBy('name')
            ->get();

        $categoriesData = $categories->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ])->values();

        return view('Admin.kategoriler', [
            'categories' => $categories,
            'categoriesData' => $categoriesData,
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::query()->create($request->validated());

        return redirect()
            ->route('admin.kategoriler')
            ->with('success', 'Kategori eklendi.');
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.kategoriler')
            ->with('success', 'Kategori güncellendi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->businesses()->exists()) {
            return redirect()
                ->route('admin.kategoriler')
                ->with('error', 'Bu kategoriye bağlı işletmeler var. Önce işletmeleri başka kategoriye taşıyın veya silin.');
        }

        $category->delete();

        return redirect()
            ->route('admin.kategoriler')
            ->with('success', 'Kategori silindi.');
    }
}
