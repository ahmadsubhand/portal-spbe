<x-admin-layout title="Review Artikel" subtitle="Prioritaskan artikel yang menunggu keputusan, berikan catatan revisi, dan publikasikan konten yang sudah disetujui.">
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    @if (session('warning'))
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <a href="{{ route('admin.artikel.validasi', ['status' => 'pending']) }}" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-300">
            <p class="text-sm font-semibold text-amber-800">Menunggu Review</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['pending'] }}</p>
            <p class="mt-2 text-xs text-amber-700">Perlu keputusan approve atau reject.</p>
        </a>
        <a href="{{ route('admin.artikel.validasi', ['status' => 'approved']) }}" class="rounded-2xl border border-sky-200 bg-sky-50 p-5 transition hover:border-sky-300">
            <p class="text-sm font-semibold text-sky-800">Siap Dipublikasikan</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['approved'] }}</p>
            <p class="mt-2 text-xs text-sky-700">Sudah disetujui dan menunggu publish.</p>
        </a>
        <a href="{{ route('admin.artikel.validasi', ['status' => 'published']) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 transition hover:border-emerald-300">
            <p class="text-sm font-semibold text-emerald-800">Sudah Dipublikasikan</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['published'] }}</p>
            <p class="mt-2 text-xs text-emerald-700">Artikel yang sudah terlihat oleh publik.</p>
        </a>
        <a href="{{ route('admin.artikel.validasi', ['status' => 'rejected']) }}" class="rounded-2xl border border-rose-200 bg-rose-50 p-5 transition hover:border-rose-300">
            <p class="text-sm font-semibold text-rose-800">Ditolak</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['rejected'] }}</p>
            <p class="mt-2 text-xs text-rose-700">Artikel yang dikembalikan dengan catatan.</p>
        </a>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Antrean Artikel</h3>
                    <p class="mt-1 text-sm text-slate-500">Urutan berdasarkan aktivitas terbaru. Gunakan filter untuk fokus pada tahap workflow tertentu.</p>
                </div>

                <form method="GET" action="{{ route('admin.artikel.validasi') }}" class="grid w-full gap-3 sm:grid-cols-[minmax(0,1fr)_210px_auto] lg:max-w-3xl">
                    <input name="q" type="search" value="{{ $filters['q'] }}" placeholder="Cari judul, penulis, atau kategori" class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <option value="attention" @selected($filters['status'] === 'attention')>Perlu perhatian</option>
                        <option value="pending" @selected($filters['status'] === 'pending')>Menunggu review</option>
                        <option value="approved" @selected($filters['status'] === 'approved')>Siap publish</option>
                        <option value="published" @selected($filters['status'] === 'published')>Dipublikasikan</option>
                        <option value="rejected" @selected($filters['status'] === 'rejected')>Ditolak</option>
                        <option value="draft" @selected($filters['status'] === 'draft')>Draft</option>
                        <option value="all" @selected($filters['status'] === 'all')>Semua artikel</option>
                    </select>
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Terapkan</button>
                </form>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($artikels as $artikel)
                @php
                    $statusLabel = $artikel->is_draft
                        ? 'Draft'
                        : ($artikel->isPublished()
                            ? 'Dipublikasikan'
                            : ($artikel->status === \App\Models\Artikel::STATUS_PENDING
                                ? 'Menunggu review'
                                : ($artikel->status === \App\Models\Artikel::STATUS_APPROVED ? 'Siap publish' : 'Ditolak')));
                    $statusClass = $artikel->is_draft
                        ? 'bg-slate-100 text-slate-700'
                        : ($artikel->isPublished()
                            ? 'bg-emerald-100 text-emerald-700'
                            : ($artikel->status === \App\Models\Artikel::STATUS_PENDING
                                ? 'bg-amber-100 text-amber-700'
                                : ($artikel->status === \App\Models\Artikel::STATUS_APPROVED ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700')));
                @endphp

                <article class="p-5 transition hover:bg-slate-50/60 sm:p-6">
                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $artikel->category?->name ?? 'Tanpa kategori' }}</span>
                                @if ($artikel->submitted_at)
                                    <span class="text-xs text-slate-400">Diajukan {{ $artikel->submitted_at->diffForHumans() }}</span>
                                @endif
                            </div>

                            <h4 class="mt-3 text-lg font-bold text-slate-900">{{ $artikel->judul }}</h4>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($artikel->konten), 220) }}</p>

                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500">
                                <span>Penulis: <strong class="font-semibold text-slate-700">{{ $artikel->user?->name ?? 'Tidak diketahui' }}</strong></span>
                                <span>Update: {{ $artikel->updated_at?->format('d M Y H:i') }}</span>
                                <span>{{ $artikel->tags->count() }} tag</span>
                            </div>

                            @if ($artikel->tags->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($artikel->tags->take(5) as $tag)
                                        <span class="rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-500">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($artikel->review_note)
                                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    <span class="font-bold">Catatan review:</span> {{ $artikel->review_note }}
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 xl:w-48 xl:flex-col">
                            <a href="{{ route('admin.artikel.show', $artikel) }}" class="flex-1 rounded-xl px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm transition hover:opacity-95" style="background-color: var(--portal-primary-deep);">
                                {{ ! $artikel->is_draft && $artikel->status === \App\Models\Artikel::STATUS_PENDING ? 'Review lengkap' : 'Lihat isi lengkap' }}
                            </a>
                            @if ($artikel->isPublished())
                                <a href="{{ route('artikel.show', $artikel) }}" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-100">Lihat publik</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h6l4.5 4.5V20.25A1.5 1.5 0 0 1 16.5 21.75h-9A1.5 1.5 0 0 1 6 20.25v-15A1.5 1.5 0 0 1 7.5 3.75Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 14 1.75 1.75L15 12" />
                        </svg>
                    </div>
                    <h4 class="mt-4 font-bold text-slate-900">Tidak ada artikel pada antrean ini</h4>
                    <p class="mt-2 text-sm text-slate-500">Ubah filter untuk melihat artikel pada tahap workflow lainnya.</p>
                    <a href="{{ route('admin.artikel.validasi', ['status' => 'all']) }}" class="mt-4 inline-flex rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700">Lihat semua artikel</a>
                </div>
            @endforelse
        </div>

        @if ($artikels->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                {{ $artikels->onEachSide(1)->links() }}
            </div>
        @endif
    </section>
</x-admin-layout>
