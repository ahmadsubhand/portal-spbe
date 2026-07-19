<x-admin-layout title="Dashboard Admin" subtitle="Pantau antrean editorial, aktivitas forum, dan pekerjaan prioritas dari satu tampilan operasional.">
    <x-slot name="actions">
        <a href="{{ route('admin.artikel.validasi') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary);">Buka Antrean Review</a>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <a href="{{ route('admin.artikel.validasi', ['status' => 'pending']) }}" class="group rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-amber-800">Menunggu Review</p>
                    <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $pendingCount }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700 transition group-hover:bg-amber-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3.75 2.25" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-amber-700">Perlu keputusan approve atau reject.</p>
        </a>

        <a href="{{ route('admin.artikel.validasi', ['status' => 'approved']) }}" class="group rounded-2xl border border-sky-200 bg-sky-50 p-5 transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-sky-800">Siap Dipublikasikan</p>
                    <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $approvedCount }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 transition group-hover:bg-sky-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-sky-700">Sudah approved dan menunggu publish.</p>
        </a>

        <a href="{{ route('admin.artikel.validasi', ['status' => 'published']) }}" class="group rounded-2xl border border-emerald-200 bg-emerald-50 p-5 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-800">Artikel Publik</p>
                    <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $publishedCount }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 transition group-hover:bg-emerald-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6S2.25 12 2.25 12Z" />
                        <circle cx="12" cy="12" r="2.25" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-emerald-700">Konten yang terlihat oleh pengunjung.</p>
        </a>

        <a href="{{ route('admin.artikel.validasi', ['status' => 'draft']) }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Draft Internal</p>
                    <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $draftCount }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-slate-600 transition group-hover:bg-slate-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h6l4.5 4.5V20.25A1.5 1.5 0 0 1 16.5 21.75h-9A1.5 1.5 0 0 1 6 20.25v-15A1.5 1.5 0 0 1 7.5 3.75Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h6M9 17.25h4.5" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-500">Masih dikerjakan sebelum diajukan.</p>
        </a>
    </div>

    <div class="mt-6 grid gap-6 2xl:grid-cols-[minmax(0,1.35fr)_minmax(300px,0.65fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-5 sm:px-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Prioritas Editorial</h3>
                    <p class="mt-1 text-sm text-slate-500">Artikel terbaru yang membutuhkan keputusan admin.</p>
                </div>
                <a href="{{ route('admin.artikel.validasi') }}" class="text-sm font-bold" style="color: var(--portal-primary);">Lihat antrean</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($articlesNeedingAttention as $artikel)
                    <article class="p-5 transition hover:bg-slate-50/60 sm:px-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $artikel->status === \App\Models\Artikel::STATUS_PENDING ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700' }}">
                                        {{ $artikel->status === \App\Models\Artikel::STATUS_PENDING ? 'Menunggu review' : 'Siap publish' }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $artikel->updated_at?->diffForHumans() }}</span>
                                </div>
                                <h4 class="mt-3 font-bold text-slate-900">{{ $artikel->judul }}</h4>
                                <p class="mt-2 text-sm text-slate-500">{{ $artikel->user?->name ?? 'Tidak diketahui' }} · {{ $artikel->category?->name ?? 'Tanpa kategori' }}</p>
                            </div>
                            <a href="{{ route('admin.artikel.show', $artikel) }}" class="flex-shrink-0 rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Tinjau lengkap</a>
                        </div>
                    </article>
                @empty
                    <div class="p-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                        </div>
                        <h4 class="mt-4 font-bold text-slate-900">Antrean editorial bersih</h4>
                        <p class="mt-2 text-sm text-slate-500">Tidak ada artikel yang membutuhkan keputusan admin saat ini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-bold text-slate-900">Aktivitas Forum</h3>
                <p class="mt-1 text-sm text-slate-500">Snapshot percakapan komunitas.</p>
                <div class="mt-5 grid grid-cols-2 gap-3 2xl:grid-cols-1">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Topik</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $forumTopicCount }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Komentar</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $forumCommentCount }}</p>
                    </div>
                </div>
                <a href="{{ route('forum.index') }}" class="mt-4 flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    <span>Lihat forum publik</span><span aria-hidden="true">→</span>
                </a>
            </section>

            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-700">Aksi Cepat</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('admin.forum.index') }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm"><span>Moderasi forum</span><span>→</span></a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm"><span>Kelola pengguna</span><span>→</span></a>
                    <a href="{{ route('admin.taxonomy.index') }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm"><span>Kelola taksonomi</span><span>→</span></a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm"><span>Periksa audit log</span><span>→</span></a>
                </div>
            </section>
        </aside>
    </div>

    <div class="mt-6 grid gap-6 2xl:grid-cols-2">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <h3 class="text-lg font-bold text-slate-900">Artikel Terbaru</h3>
                <p class="mt-1 text-sm text-slate-500">Perubahan terbaru dari seluruh tahap editorial.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($latestArticles as $artikel)
                    <div class="flex items-start justify-between gap-4 px-5 py-4 sm:px-6">
                        <div class="min-w-0">
                            <h4 class="truncate text-sm font-bold text-slate-900">{{ $artikel->judul }}</h4>
                            <p class="mt-1 text-xs text-slate-500">{{ $artikel->user?->name ?? 'Tidak diketahui' }} · {{ $artikel->updated_at?->format('d M Y H:i') }}</p>
                        </div>
                        <span class="flex-shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                            {{ $artikel->is_draft ? 'Draft' : ($artikel->isPublished() ? 'Publik' : ($artikel->status === \App\Models\Artikel::STATUS_PENDING ? 'Review' : ($artikel->status === \App\Models\Artikel::STATUS_APPROVED ? 'Approved' : 'Rejected'))) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">Belum ada artikel.</div>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-5 sm:px-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Topik Forum Terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">Percakapan yang baru dibuka.</p>
                </div>
                <a href="{{ route('forum.index') }}" class="text-sm font-bold" style="color: var(--portal-primary);">Lihat forum</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($latestTopics as $topic)
                    <div class="px-5 py-4 sm:px-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-bold text-slate-900">{{ $topic->title }}</h4>
                                <p class="mt-1 text-xs text-slate-500">{{ $topic->user?->name ?? 'Tidak diketahui' }} · {{ $topic->created_at?->format('d M Y H:i') }}</p>
                            </div>
                            <span class="flex-shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $topic->replies_count }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">Belum ada topik forum.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-admin-layout>
