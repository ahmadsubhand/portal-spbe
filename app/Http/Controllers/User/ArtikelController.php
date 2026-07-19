<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtikelRequest;
use App\Http\Requests\UpdateArtikelRequest;
use App\Http\Requests\UploadArticleMediaRequest;
use App\Models\Artikel;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Topic;
use App\Services\AuditLogService;
use App\Support\RichContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected RichContentSanitizer $richContentSanitizer
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', $request->query('q', '')));
        $selectedCategories = collect(Arr::wrap($request->query('category', [])))->filter()->values()->all();
        $selectedTags = collect(Arr::wrap($request->query('tag', [])))->filter()->values()->all();
        $selectedFormats = collect(Arr::wrap($request->query('format', [])))
            ->filter(fn ($format) => in_array($format, ['text', 'image', 'video'], true))
            ->values()
            ->all();
        $sort = $request->string('sort', 'latest')->toString();

        $artikels = Artikel::query()
            ->published()
            ->with(['category', 'tags', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('judul', 'like', "%{$search}%")
                        ->orWhere('konten', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('tags', fn ($tagQuery) => $tagQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($selectedCategories !== [], function ($query) use ($selectedCategories) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->whereIn('slug', $selectedCategories));
            })
            ->when($selectedTags !== [], function ($query) use ($selectedTags) {
                $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereIn('slug', $selectedTags));
            })
            ->when($selectedFormats !== [], function ($query) use ($selectedFormats) {
                $query->where(function ($formatQuery) use ($selectedFormats): void {
                    foreach ($selectedFormats as $format) {
                        $formatQuery->orWhere(function ($typeQuery) use ($format): void {
                            match ($format) {
                                'image' => $typeQuery->where('konten', 'like', '%<img%'),
                                'video' => $typeQuery->where('konten', 'like', '%<iframe%'),
                                default => $typeQuery
                                    ->where('konten', 'not like', '%<img%')
                                    ->where('konten', 'not like', '%<iframe%'),
                            };
                        });
                    }
                });
            })
            ->when($sort === 'oldest', fn ($query) => $query->oldest('published_at'))
            ->when($sort !== 'oldest', fn ($query) => $query->latest('published_at'))
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->withCount([
                'artikels as published_articles_count' => fn ($query) => $query->published(),
            ])
            ->orderByDesc('published_articles_count')
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->published_articles_count > 0)
            ->values();

        $tags = Tag::query()
            ->withCount([
                'artikels as published_articles_count' => fn ($query) => $query->published(),
            ])
            ->orderByDesc('published_articles_count')
            ->orderBy('name')
            ->take(18)
            ->get()
            ->filter(fn ($tag) => $tag->published_articles_count > 0)
            ->values();

        return view('artikel.index', [
            'artikels' => $artikels,
            'categories' => $categories,
            'tags' => $tags,
            'filters' => [
                'search' => $search,
                'categories' => $selectedCategories,
                'tags' => $selectedTags,
                'formats' => $selectedFormats,
                'sort' => $sort,
            ],
            'formatCounts' => [
                'text' => Artikel::published()->where('konten', 'not like', '%<img%')->where('konten', 'not like', '%<iframe%')->count(),
                'image' => Artikel::published()->where('konten', 'like', '%<img%')->count(),
                'video' => Artikel::published()->where('konten', 'like', '%<iframe%')->count(),
            ],
        ]);
    }

    public function show(Artikel $artikel): View
    {
        abort_unless($artikel->isPublished(), 404);

        $artikel->load(['category', 'tags', 'user']);

        $contentWithBlockSpacing = preg_replace(
            '/<\/?(?:h[1-6]|p|blockquote|li|figure|iframe|ul|ol)[^>]*>/i',
            ' ',
            $artikel->konten
        );
        $cleanContent = trim(preg_replace('/\s+/', ' ', strip_tags($contentWithBlockSpacing ?? $artikel->konten)));
        $wordCount = max(str_word_count($cleanContent), 1);
        $estimatedReadMinutes = max((int) ceil($wordCount / 180), 1);

        return view('artikel.show', [
            'artikel' => $artikel,
            'estimatedReadMinutes' => $estimatedReadMinutes,
            'guestSummary' => Str::words($cleanContent, 55, '...'),
        ]);
    }

    public function dashboard(): View
    {
        $this->authorize('viewAnyInternal', Artikel::class);

        $userId = Auth::id();
        $articles = Artikel::query()->where('user_id', $userId);

        return view('asn.dashboard', [
            'stats' => [
                'drafts' => (clone $articles)->where('is_draft', true)->count(),
                'in_review' => (clone $articles)->pendingReview()->count(),
                'published' => (clone $articles)->published()->count(),
                'needs_revision' => (clone $articles)->where('is_draft', false)->where('status', Artikel::STATUS_REJECTED)->count(),
            ],
            'recentArticles' => (clone $articles)->with('category')->latest('updated_at')->take(5)->get(),
            'recentTopics' => Topic::query()->where('user_id', $userId)->withCount(['replies', 'likes'])->latest()->take(4)->get(),
        ]);
    }

    public function internalIndex(Request $request): View
    {
        $this->authorize('viewAnyInternal', Artikel::class);

        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $userId = Auth::id();

        $artikels = Artikel::query()
            ->with(['category', 'tags'])
            ->where('user_id', $userId)
            ->when($search !== '', fn ($query) => $query->where('judul', 'like', "%{$search}%"))
            ->when($status === 'draft', fn ($query) => $query->where('is_draft', true))
            ->when($status === 'review', fn ($query) => $query->pendingReview())
            ->when($status === 'published', fn ($query) => $query->published())
            ->when($status === 'approved', fn ($query) => $query->where('is_draft', false)->where('status', Artikel::STATUS_APPROVED)->whereNull('published_at'))
            ->when($status === 'rejected', fn ($query) => $query->where('is_draft', false)->where('status', Artikel::STATUS_REJECTED))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseQuery = Artikel::query()->where('user_id', $userId);

        return view('artikel.manage', [
            'artikels' => $artikels,
            'filters' => compact('search', 'status'),
            'stats' => [
                'all' => (clone $baseQuery)->count(),
                'draft' => (clone $baseQuery)->where('is_draft', true)->count(),
                'review' => (clone $baseQuery)->pendingReview()->count(),
                'published' => (clone $baseQuery)->published()->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Artikel::class);

        return view('artikel.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArtikelRequest $request): RedirectResponse
    {
        $this->authorize('create', Artikel::class);

        $artikel = Artikel::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'konten' => $this->richContentSanitizer->sanitize($request->konten),
            'category_id' => $request->integer('category_id') ?: null,
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => true,
        ]);

        $artikel->tags()->sync($request->input('tags', []));
        $this->auditLogService->record($request->user(), 'article.created', Artikel::class, $artikel->id);

        return redirect()->route('portal.artikel.index')
            ->with('success', 'Draft artikel berhasil disimpan.')
            ->with('clear_article_draft', 'portal-spbe:article-draft:'.Auth::id().':new');
    }

    public function edit(Artikel $artikel): View
    {
        $this->authorize('update', $artikel);

        return view('artikel.edit', [
            'artikel' => $artikel->load('tags'),
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArtikelRequest $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('update', $artikel);

        $artikel->update([
            'judul' => $request->judul,
            'konten' => $this->richContentSanitizer->sanitize($request->konten),
            'category_id' => $request->integer('category_id') ?: null,
        ]);
        $artikel->tags()->sync($request->input('tags', []));
        $this->auditLogService->record($request->user(), 'article.updated', Artikel::class, $artikel->id);

        return redirect()->route('portal.artikel.index')
            ->with('success', 'Draft artikel berhasil diperbarui.')
            ->with('clear_article_draft', 'portal-spbe:article-draft:'.Auth::id().':'.$artikel->id);
    }

    public function submit(Request $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('submit', $artikel);

        $artikel->update([
            'is_draft' => false,
            'status' => Artikel::STATUS_PENDING,
            'review_note' => null,
            'submitted_at' => now(),
        ]);
        $this->auditLogService->record($request->user(), 'article.submitted', Artikel::class, $artikel->id);

        return redirect()->route('portal.artikel.index')->with('success', 'Artikel berhasil diajukan untuk ditinjau admin.');
    }

    public function destroy(Request $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('delete', $artikel);

        $articleId = $artikel->id;
        $artikel->delete();
        $this->auditLogService->record($request->user(), 'article.deleted', Artikel::class, $articleId);

        return redirect()->route('portal.artikel.index')
            ->with('success', 'Draft artikel berhasil dihapus.')
            ->with('clear_article_draft', 'portal-spbe:article-draft:'.Auth::id().':'.$articleId);
    }

    public function uploadMedia(UploadArticleMediaRequest $request): JsonResponse
    {
        $this->authorize('create', Artikel::class);

        $path = $request->file('image')->storePublicly('article-media', 'public');

        if (! $path) {
            return response()->json([
                'message' => 'Gambar gagal disimpan. Periksa konfigurasi penyimpanan server.',
            ], 500);
        }

        return response()->json([
            // Relative URLs follow the current host and port used by the browser.
            'url' => '/storage/'.$path,
            'path' => $path,
            'message' => 'Gambar berhasil diunggah.',
        ]);
    }
}
