<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveTopicRequest;
use App\Models\Topic;
use App\Models\TopicLike;
use App\Models\Reply;
use App\Models\ReplyLike;
use App\Services\AuditLogService;
use App\Support\RichContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class ForumController extends Controller
{
    public function __construct(
        protected RichContentSanitizer $richContentSanitizer,
        protected AuditLogService $auditLogService
    )
    {
    }
    /**
     * Menampilkan Halaman Utama / List Forum Diskusi
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedCategories = collect(Arr::wrap($request->query('category', [])))->filter()->values()->all();
        $selectedStatuses = collect(Arr::wrap($request->query('status', [])))
            ->filter(fn ($status) => in_array($status, ['answered', 'unanswered'], true))
            ->values()
            ->all();
        $selectedFormats = collect(Arr::wrap($request->query('format', [])))
            ->filter(fn ($format) => in_array($format, ['text', 'image', 'video'], true))
            ->values()
            ->all();
        $sort = (string) $request->query('sort', 'latest');

        $query = Topic::query()
            ->active()
            ->with('user')
            ->withCount(['replies', 'likes'])
            ->when($search !== '', function ($topicQuery) use ($search) {
                $topicQuery->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($selectedCategories !== [], fn ($topicQuery) => $topicQuery->whereIn('category', $selectedCategories))
            ->when($selectedStatuses !== [], function ($topicQuery) use ($selectedStatuses): void {
                $topicQuery->where(function ($statusQuery) use ($selectedStatuses): void {
                    if (in_array('answered', $selectedStatuses, true)) {
                        $statusQuery->orHas('replies', '>', 0);
                    }
                    if (in_array('unanswered', $selectedStatuses, true)) {
                        $statusQuery->orDoesntHave('replies');
                    }
                });
            })
            ->when($selectedFormats !== [], function ($topicQuery) use ($selectedFormats): void {
                $topicQuery->where(function ($formatQuery) use ($selectedFormats): void {
                    foreach ($selectedFormats as $format) {
                        $formatQuery->orWhere(function ($typeQuery) use ($format): void {
                            match ($format) {
                                'image' => $typeQuery->where('content', 'like', '%<img%'),
                                'video' => $typeQuery->where('content', 'like', '%<iframe%'),
                                default => $typeQuery->where('content', 'not like', '%<img%')->where('content', 'not like', '%<iframe%'),
                            };
                        });
                    }
                });
            });

        if ($sort === 'popular') {
            $query->orderByDesc('views_count')->orderByDesc('created_at');
        } elseif ($sort === 'active') {
            $query->orderByDesc(
                Reply::query()->select('created_at')->whereColumn('topic_id', 'topics.id')->latest()->limit(1)
            )->latest();
        } else {
            $query->latest();
        }

        $topics = $query->paginate(10)->withQueryString();

        $visibilityScope = fn ($q) => $q;

        $categoryCounts = Topic::query()
            ->active()
            ->tap($visibilityScope)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->orderBy('category')
            ->get();

        $labelCounts = Topic::query()
            ->active()
            ->latest()
            ->take(30)
            ->get()
            ->flatMap(function (Topic $topic) {
                return collect(preg_split('/\s+/', $topic->title.' '.$topic->content))
                    ->map(fn ($word) => Str::lower(trim($word, " \t\n\r\0\x0B.,:;!?\"'()[]{}#")))
                    ->filter(fn ($word) => strlen($word) >= 5)
                    ->reject(fn ($word) => in_array($word, [
                        'dengan', 'untuk', 'dalam', 'yang', 'pada', 'dari', 'forum', 'diskusi',
                        'aplikasi', 'artikel', 'tentang', 'karena', 'secara', 'lebih', 'akan',
                    ], true));
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        return view('forum.index', [
            'topics' => $topics,
            'categoryCounts' => $categoryCounts,
            'labelCounts' => $labelCounts,
            'filters' => [
                'search' => $search,
                'categories' => $selectedCategories,
                'statuses' => $selectedStatuses,
                'formats' => $selectedFormats,
                'sort' => $sort,
            ],
            'statusCounts' => [
                'answered' => Topic::query()->active()->tap($visibilityScope)->has('replies', '>', 0)->count(),
                'unanswered' => Topic::query()->active()->tap($visibilityScope)->doesntHave('replies')->count(),
            ],
            'formatCounts' => [
                'text' => Topic::query()->active()->tap($visibilityScope)->where('content', 'not like', '%<img%')->where('content', 'not like', '%<iframe%')->count(),
                'image' => Topic::query()->active()->tap($visibilityScope)->where('content', 'like', '%<img%')->count(),
                'video' => Topic::query()->active()->tap($visibilityScope)->where('content', 'like', '%<iframe%')->count(),
            ],
        ]);
    }

    /**
     * Tampilan 1: Menampilkan Form Buat Topik
     */
    public function create()
    {
        // Langsung return ke file view form pembuatan topik
        return view('forum.create');
    }

    public function myTopics(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $category = (string) $request->query('category', '');
        $userId = Auth::id();

        $topics = Topic::query()
            ->where('user_id', $userId)
            ->withCount(['replies', 'likes'])
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('forum.my-topics', [
            'topics' => $topics,
            'filters' => compact('search', 'category'),
            'stats' => [
                'topics' => Topic::query()->where('user_id', $userId)->count(),
                'replies' => Reply::query()->whereHas('topic', fn ($query) => $query->where('user_id', $userId))->count(),
                'views' => Topic::query()->where('user_id', $userId)->sum('views_count'),
            ],
        ]);
    }

    public function edit(int $id): View
    {
        $topic = Topic::query()
            ->when(! Auth::user()->isAdmin(), fn ($query) => $query->where('user_id', Auth::id()))
            ->findOrFail($id);
        $this->authorize('update', $topic);

        return view('forum.edit', compact('topic'));
    }

    public function update(SaveTopicRequest $request, int $id): RedirectResponse
    {
        $topic = Topic::query()
            ->when(! Auth::user()->isAdmin(), fn ($query) => $query->where('user_id', Auth::id()))
            ->findOrFail($id);
        $this->authorize('update', $topic);
        $validated = $request->validated();
        $topic->update([
            ...$validated,
            'content' => $this->richContentSanitizer->sanitize($validated['content']),
        ]);

        return redirect()->route('forum.my-topics')->with('success', 'Topik diskusi berhasil diperbarui.')
            ->with('clear_article_draft', 'portal-spbe:forum-draft:'.Auth::id().':'.$topic->id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $topic = Topic::query()->findOrFail($id);
        $this->authorize('delete', $topic);

        $topicId = $topic->id;
        $topic->delete();
        $this->auditLogService->record(Auth::user(), 'topic.deleted', Topic::class, $topicId);

        return redirect()->route(Auth::user()->isAdmin() ? 'forum.index' : 'forum.my-topics')
            ->with('success', 'Topik diskusi berhasil dihapus.');
    }

    public function toggleActive(Topic $topic): RedirectResponse
    {
        $this->authorize('toggleActive', $topic);

        $topic->update(['is_active' => ! $topic->is_active]);
        $action = $topic->is_active ? 'topic.activated' : 'topic.deactivated';
        $this->auditLogService->record(Auth::user(), $action, Topic::class, $topic->id);

        return back()->with('success', $topic->is_active
            ? 'Topik diskusi diaktifkan dan tampil kembali.'
            : 'Topik diskusi dinonaktifkan dari portal.');
    }

    /**
     * Tampilan 2: Memproses data inputan dan menyimpannya ke Database
     */
    public function store(SaveTopicRequest $request)
    {
        // 1. VALIDASI DATA INPUTAN
        // Memastikan user mengisi semua field penting dan membatasi jumlah karakter judul.
        $validated = $request->validated();

        // 2. LOGIKA PEMBUATAN SLUG UNIK
        // Mengubah "Judul Topik Saya" menjadi "judul-topik-saya-1234" agar URL-nya rapi (SEO-friendly)
        // Catatan: Jika Anda sudah memasang boot Str::slug() di Model Topic seperti langkah sebelumnya, 
        // Laravel akan membuatnya otomatis, namun membuat secara eksplisit di sini juga aman.
        $slug = Str::slug($request->title) . '-' . rand(1000, 9999);

        // 3. MENYIMPAN KE DATABASE (ELOQUENT)
        Topic::create([
            'user_id' => Auth::id(), // Mengambil ID user yang sedang login saat ini
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'content' => $this->richContentSanitizer->sanitize($validated['content']),
            'visibility' => $validated['visibility'],
            'views_count' => 0 // Inisialisasi awal jumlah view
        ]);

        // 4. REDIRECT + ALERT SUCCESS
        // Setelah sukses disimpan, tendang pengguna kembali ke halaman utama forum dengan pesan sukses.
        return redirect()->route('forum.index')->with('success', 'Topik diskusi berhasil diterbitkan!')
            ->with('clear_article_draft', 'portal-spbe:forum-draft:'.Auth::id().':new');
    }

    /**
     * Menampilkan Halaman Detail Berdasarkan Slug
     */
    public function show(string $slug): View|RedirectResponse
    {
        $topic = Topic::with([
            'user',
            'likes',
            'replies' => fn ($query) => $query->active()->whereNull('parent_id')->oldest(),
            'replies.user',
            'replies.likes',
            'replies.children' => fn ($query) => $query->active()->oldest(),
            'replies.children.user',
            'replies.children.likes',
        ])->where('slug', $slug)->firstOrFail();

        if (! $topic->isActive()) {
            abort_unless(Auth::check() && Auth::user()->can('update', $topic), 404);
        }

        // Topik tertutup memaksa guest login
        if (! $topic->isOpen() && ! Auth::check()) {
            return redirect()->route('login');
        }

        $topic->increment('views_count');

        $preview = Str::of(strip_tags($topic->content))->limit(280, ' ...');

        return view('forum.show', [
            'topic' => $topic,
            'preview' => (string) $preview,
        ]);
    }

    /**
     * Fitur Like dan Unlike Diskusi
     */
    public function toggleLike(Request $request, $id)
    {
        Topic::query()->active()->findOrFail($id);
        // Cari tahu apakah user yang sedang login sudah pernah memberikan like ke topik ini
        $likeExists = TopicLike::where('topic_id', $id)
                               ->where('user_id', Auth::id())
                               ->first();

        if ($likeExists) {
            // Jika sudah ada record-nya, berarti user ingin membatalkan (Unlike)
            $likeExists->delete();
        } else {
            // Jika belum ada record-nya, buat data like baru (Like)
            TopicLike::create([
                'topic_id' => $id,
                'user_id' => Auth::id()
            ]);
        }

        // Kembalikan user ke posisi semula di halaman detail topik
        return redirect()->to($request->input('return_to', url()->previous()));
    }

    /**
     * Menyimpan Balasan (Komentar) Baru dari Pengguna
     */
    public function storeReply(Request $request, $id)
    {
        Topic::query()->active()->findOrFail($id);
        // 1. Validasi isi teks balasan
        $request->validate([
            'content' => 'required|string|min:2',
            'parent_id' => ['nullable', 'integer', 'exists:replies,id'],
        ], [
            'content.required' => 'Kolom balasan tidak boleh kosong.',
            'content.min' => 'Balasan terlalu pendek, tuliskan minimal 2 karakter.'
        ]);

        // 2. Simpan data komentar ke database
        $parent = $request->filled('parent_id')
            ? Reply::query()->active()->where('topic_id', $id)->whereNull('parent_id')->findOrFail($request->integer('parent_id'))
            : null;

        Reply::create([
            'topic_id' => $id,            // Mengaitkan komentar dengan ID Topik terkait
            'parent_id' => $parent?->id,
            'user_id' => Auth::id(),      // Melacak siapa ASN/Admin yang membalas
            'content' => $request->content // Isi teks balasan
        ]);

        // 3. Kembalikan ke halaman detail dengan pesan sukses
        return redirect()->back()->with('success', 'Balasan Anda berhasil dikirim!');
    }

    public function toggleReplyLike(Request $request, Reply $reply): RedirectResponse
    {
        abort_unless($reply->topic()->active()->exists() && $reply->isActive(), 404);

        $like = ReplyLike::query()
            ->where('reply_id', $reply->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
        } else {
            ReplyLike::create(['reply_id' => $reply->id, 'user_id' => Auth::id()]);
        }

        return redirect()->to($request->input('return_to', url()->previous()));
    }
}
