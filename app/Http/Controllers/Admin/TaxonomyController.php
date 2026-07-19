<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Services\TaxonomyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $categorySearch = trim((string) $request->string('category_q'));
        $tagSearch = trim((string) $request->string('tag_q'));

        return view('admin.taxonomy.index', [
            'categories' => Category::query()
                ->withCount('artikels')
                ->when($categorySearch !== '', function ($query) use ($categorySearch): void {
                    $query->where(function ($categoryQuery) use ($categorySearch): void {
                        $categoryQuery->where('name', 'like', '%'.$categorySearch.'%')
                            ->orWhere('slug', 'like', '%'.$categorySearch.'%')
                            ->orWhere('description', 'like', '%'.$categorySearch.'%');
                    });
                })
                ->orderBy('name')
                ->paginate(8, ['*'], 'categories_page')
                ->withQueryString(),
            'tags' => Tag::query()
                ->withCount('artikels')
                ->when($tagSearch !== '', function ($query) use ($tagSearch): void {
                    $query->where(function ($tagQuery) use ($tagSearch): void {
                        $tagQuery->where('name', 'like', '%'.$tagSearch.'%')
                            ->orWhere('slug', 'like', '%'.$tagSearch.'%');
                    });
                })
                ->orderBy('name')
                ->paginate(12, ['*'], 'tags_page')
                ->withQueryString(),
            'filters' => [
                'category_q' => $categorySearch,
                'tag_q' => $tagSearch,
            ],
            'stats' => [
                'categories' => Category::count(),
                'tags' => Tag::count(),
                'used_categories' => Category::has('artikels')->count(),
                'used_tags' => Tag::has('artikels')->count(),
            ],
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request, TaxonomyService $taxonomyService): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $taxonomyService->createCategory($request->validated());

        return redirect()->route('admin.taxonomy.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(UpdateCategoryRequest $request, Category $category, TaxonomyService $taxonomyService): RedirectResponse
    {
        $this->authorize('update', $category);

        $taxonomyService->updateCategory($category, $request->validated());

        return redirect()->route('admin.taxonomy.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function storeTag(StoreTagRequest $request, TaxonomyService $taxonomyService): RedirectResponse
    {
        $this->authorize('create', Tag::class);

        $taxonomyService->createTag($request->validated());

        return redirect()->route('admin.taxonomy.index')->with('status', 'Label berhasil ditambahkan.');
    }

    public function updateTag(UpdateTagRequest $request, Tag $tag, TaxonomyService $taxonomyService): RedirectResponse
    {
        $this->authorize('update', $tag);

        $taxonomyService->updateTag($tag, $request->validated());

        return redirect()->route('admin.taxonomy.index')->with('status', 'Label berhasil diperbarui.');
    }
}
