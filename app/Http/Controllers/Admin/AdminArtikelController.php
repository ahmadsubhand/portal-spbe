<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectArtikelRequest;
use App\Models\Artikel;
use App\Models\Reply;
use App\Models\Topic;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminArtikelController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function dashboard(): View
    {
        $pendingCount = Artikel::pendingReview()->count();
        $approvedCount = Artikel::query()
            ->where('is_draft', false)
            ->where('status', Artikel::STATUS_APPROVED)
            ->whereNull('published_at')
            ->count();
        $publishedCount = Artikel::published()->count();
        $draftCount = Artikel::query()->where('is_draft', true)->count();

        $forumTopicCount = Topic::count();
        $forumCommentCount = Reply::count();

        $latestArticles = Artikel::with(['user', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        $articlesNeedingAttention = Artikel::with(['user', 'category'])
            ->where(function ($query) {
                $query->pendingReview()
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('is_draft', false)
                            ->where('status', Artikel::STATUS_APPROVED)
                            ->whereNull('published_at');
                    });
            })
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $latestTopics = Topic::with(['user'])
            ->withCount('replies')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingCount',
            'approvedCount',
            'publishedCount',
            'draftCount',
            'forumTopicCount',
            'forumCommentCount',
            'latestArticles',
            'articlesNeedingAttention',
            'latestTopics',
        ));
    }

    public function indexValidasi(Request $request): View
    {
        $this->authorize('review', Artikel::class);

        $status = trim((string) $request->string('status', 'attention'));
        $search = trim((string) $request->string('q'));

        $artikels = Artikel::with(['user', 'category', 'tags'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($articleQuery) use ($search): void {
                    $articleQuery->where('judul', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($status === 'attention', function ($query): void {
                $query->where(function ($attentionQuery): void {
                    $attentionQuery->pendingReview()
                        ->orWhere(function ($approvedQuery): void {
                            $approvedQuery->where('is_draft', false)
                                ->where('status', Artikel::STATUS_APPROVED)
                                ->whereNull('published_at');
                        });
                });
            })
            ->when($status === 'pending', fn ($query) => $query->pendingReview())
            ->when($status === 'approved', function ($query): void {
                $query->where('is_draft', false)
                    ->where('status', Artikel::STATUS_APPROVED)
                    ->whereNull('published_at');
            })
            ->when($status === 'published', fn ($query) => $query->published())
            ->when($status === 'rejected', function ($query): void {
                $query->where('is_draft', false)->where('status', Artikel::STATUS_REJECTED);
            })
            ->when($status === 'draft', fn ($query) => $query->where('is_draft', true))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.artikel.review', [
            'artikels' => $artikels,
            'filters' => [
                'status' => $status,
                'q' => $search,
            ],
            'stats' => [
                'pending' => Artikel::pendingReview()->count(),
                'approved' => Artikel::query()
                    ->where('is_draft', false)
                    ->where('status', Artikel::STATUS_APPROVED)
                    ->whereNull('published_at')
                    ->count(),
                'published' => Artikel::published()->count(),
                'rejected' => Artikel::query()
                    ->where('is_draft', false)
                    ->where('status', Artikel::STATUS_REJECTED)
                    ->count(),
            ],
        ]);
    }

    public function show(Artikel $artikel): View
    {
        $this->authorize('review', Artikel::class);

        $artikel->load(['user', 'category', 'tags']);
        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags($artikel->konten)) ?? '');

        return view('admin.artikel.show', [
            'artikel' => $artikel,
            'estimatedReadMinutes' => max(1, (int) ceil(str_word_count($plainContent) / 200)),
        ]);
    }

    public function approve(Artikel $artikel): RedirectResponse
    {
        $this->authorize('review', Artikel::class);

        if ($artikel->is_draft || $artikel->status !== Artikel::STATUS_PENDING) {
            return back()->with('warning', 'Hanya artikel yang sedang menunggu review yang dapat disetujui.');
        }

        $artikel->update([
            'status' => Artikel::STATUS_APPROVED,
            'review_note' => null,
            'approved_at' => now(),
        ]);
        $this->auditLogService->record(request()->user(), 'article.approved', Artikel::class, $artikel->id);

        return back()->with('success', 'Artikel disetujui.');
    }

    public function reject(RejectArtikelRequest $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('review', Artikel::class);

        if ($artikel->is_draft || $artikel->status !== Artikel::STATUS_PENDING) {
            return back()->with('warning', 'Hanya artikel yang sedang menunggu review yang dapat ditolak.');
        }

        $artikel->update([
            'status' => Artikel::STATUS_REJECTED,
            'is_draft' => false,
            'review_note' => $request->string('review_note')->toString(),
            'approved_at' => null,
            'published_at' => null,
        ]);
        $this->auditLogService->record(request()->user(), 'article.rejected', Artikel::class, $artikel->id, [
            'review_note' => $request->string('review_note')->toString(),
        ]);

        return back()->with('warning', 'Artikel ditolak.');
    }

    public function publish(Artikel $artikel): RedirectResponse
    {
        $this->authorize('publish', $artikel);

        $artikel->update([
            'published_at' => now(),
        ]);
        $this->auditLogService->record(request()->user(), 'article.published', Artikel::class, $artikel->id);

        return back()->with('success', 'Artikel dipublikasikan.');
    }

    public function unpublish(Artikel $artikel): RedirectResponse
    {
        $this->authorize('unpublish', $artikel);

        $artikel->update(['published_at' => null]);
        $this->auditLogService->record(request()->user(), 'article.unpublished', Artikel::class, $artikel->id);

        return back()->with('warning', 'Artikel dinonaktifkan dari portal publik dan tetap berstatus disetujui.');
    }

    public function destroy(Artikel $artikel): RedirectResponse
    {
        $this->authorize('delete', $artikel);

        $articleId = $artikel->id;
        $artikel->delete();
        $this->auditLogService->record(request()->user(), 'article.deleted', Artikel::class, $articleId);

        return redirect()->route('admin.artikel.validasi', ['status' => 'all'])
            ->with('warning', 'Artikel dihapus permanen.');
    }
}
