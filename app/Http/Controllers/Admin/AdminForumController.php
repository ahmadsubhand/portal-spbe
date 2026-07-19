<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use App\Models\Topic;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminForumController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));

        $topics = Topic::query()
            ->with(['user'])
            ->withCount(['replies', 'likes'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($topicQuery) use ($search): void {
                    $topicQuery->where('title', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($status === 'open', fn ($query) => $query->where('visibility', 'open'))
            ->when($status === 'closed', fn ($query) => $query->where('visibility', 'closed'))
            ->when($status === 'answered', fn ($query) => $query->has('replies'))
            ->when($status === 'unanswered', fn ($query) => $query->doesntHave('replies'))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.forum.index', [
            'topics' => $topics,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
            'stats' => [
                'all' => Topic::count(),
                'active' => Topic::query()->where('is_active', true)->count(),
                'inactive' => Topic::query()->where('is_active', false)->count(),
                'open' => Topic::query()->where('visibility', 'open')->count(),
                'closed' => Topic::query()->where('visibility', 'closed')->count(),
            ],
        ]);
    }

    public function show(Topic $topic): View
    {
        $topic->load([
            'user',
            'likes',
            'replies' => fn ($query) => $query->whereNull('parent_id')->oldest(),
            'replies.user',
            'replies.likes',
            'replies.children.user',
            'replies.children.likes',
        ]);

        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags($topic->content)) ?? '');

        return view('admin.forum.show', [
            'topic' => $topic,
            'estimatedReadMinutes' => max(1, (int) ceil(str_word_count($plainContent) / 180)),
            'summary' => Str::words($plainContent, 50, '...'),
        ]);
    }

    public function toggleActive(Topic $topic): RedirectResponse
    {
        $this->authorize('toggleActive', $topic);

        $topic->update(['is_active' => ! $topic->is_active]);
        $this->auditLogService->record(request()->user(), $topic->is_active ? 'topic.activated' : 'topic.deactivated', Topic::class, $topic->id);

        return back()->with('success', $topic->is_active
            ? 'Topik diaktifkan kembali.'
            : 'Topik dinonaktifkan dari portal publik.');
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        $topicId = $topic->id;
        $topic->delete();
        $this->auditLogService->record(request()->user(), 'topic.deleted', Topic::class, $topicId);

        return redirect()->route('admin.forum.index')->with('warning', 'Topik diskusi dihapus permanen.');
    }

    public function toggleReplyActive(Reply $reply): RedirectResponse
    {
        $this->authorize('toggleActive', $reply);

        $reply->update(['is_active' => ! $reply->is_active]);
        $this->auditLogService->record(request()->user(), $reply->is_active ? 'reply.activated' : 'reply.deactivated', Reply::class, $reply->id);

        return back()->with('success', $reply->is_active
            ? 'Balasan diaktifkan kembali.'
            : 'Balasan dinonaktifkan dari portal publik.');
    }

    public function destroyReply(Reply $reply): RedirectResponse
    {
        $this->authorize('delete', $reply);

        $replyId = $reply->id;
        $reply->delete();
        $this->auditLogService->record(request()->user(), 'reply.deleted', Reply::class, $replyId);

        return back()->with('warning', 'Balasan dihapus permanen.');
    }
}
