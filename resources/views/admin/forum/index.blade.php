<x-admin-layout title="Moderasi Forum" subtitle="Pantau seluruh topik diskusi, nonaktifkan konten yang perlu ditarik, dan hapus topik yang melanggar kebijakan.">
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <a href="{{ route('admin.forum.index', ['status' => 'all']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-slate-300">
            <p class="text-sm font-semibold text-slate-700">Semua Topik</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['all'] }}</p>
        </a>
        <a href="{{ route('admin.forum.index', ['status' => 'active']) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 transition hover:border-emerald-300">
            <p class="text-sm font-semibold text-emerald-800">Aktif</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['active'] }}</p>
        </a>
        <a href="{{ route('admin.forum.index', ['status' => 'inactive']) }}" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-300">
            <p class="text-sm font-semibold text-amber-800">Nonaktif</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['inactive'] }}</p>
        </a>
        <a href="{{ route('admin.forum.index', ['status' => 'open']) }}" class="rounded-2xl border border-sky-200 bg-sky-50 p-5 transition hover:border-sky-300">
            <p class="text-sm font-semibold text-sky-800">Terbuka</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['open'] }}</p>
        </a>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Topik Diskusi</h3>
                    <p class="mt-1 text-sm text-slate-500">Gunakan filter untuk mencari topik, memeriksa status, dan membuka detail diskusi.</p>
                </div>

                <form method="GET" action="{{ route('admin.forum.index') }}" class="grid w-full gap-3 sm:grid-cols-[minmax(0,1fr)_210px_auto] lg:max-w-3xl">
                    <input name="q" type="search" value="{{ $filters['q'] }}" placeholder="Cari judul, penulis, atau kategori" class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <option value="all" @selected($filters['status'] === 'all')>Semua status</option>
                        <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Nonaktif</option>
                        <option value="open" @selected($filters['status'] === 'open')>Terbuka</option>
                        <option value="closed" @selected($filters['status'] === 'closed')>Tertutup</option>
                        <option value="answered" @selected($filters['status'] === 'answered')>Ada balasan</option>
                        <option value="unanswered" @selected($filters['status'] === 'unanswered')>Belum dibalas</option>
                    </select>
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Terapkan</button>
                </form>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($topics as $topic)
                <article class="p-5 transition hover:bg-slate-50/60 sm:p-6">
                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $topic->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $topic->isActive() ? 'Aktif' : 'Nonaktif' }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $topic->visibility === 'open' ? 'Terbuka' : 'Tertutup' }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $topic->category }}</span>
                                <span class="text-xs text-slate-400">{{ $topic->updated_at?->diffForHumans() }}</span>
                            </div>

                            <h4 class="mt-3 text-lg font-bold text-slate-900">{{ $topic->title }}</h4>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($topic->content), 220) }}</p>

                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500">
                                <span>Penulis: <strong class="font-semibold text-slate-700">{{ $topic->user?->name ?? 'Tidak diketahui' }}</strong></span>
                                <span>Update: {{ $topic->updated_at?->format('d M Y H:i') }}</span>
                                <span>{{ $topic->replies_count }} balasan</span>
                                <span>{{ $topic->likes_count }} suka</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 xl:w-52 xl:flex-col">
                            <a href="{{ route('admin.forum.show', $topic) }}" class="flex-1 rounded-xl px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm transition hover:opacity-95" style="background-color: var(--portal-primary-deep);">Lihat detail</a>
                            @if ($topic->isActive())
                                <a href="{{ route('forum.show', $topic->slug) }}" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-100">Lihat publik</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 6.75h9A3.75 3.75 0 0 1 20.25 10.5v4.5A3.75 3.75 0 0 1 16.5 18.75H12l-4.5 3v-3H7.5A3.75 3.75 0 0 1 3.75 15v-4.5A3.75 3.75 0 0 1 7.5 6.75Z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 font-bold text-slate-900">Tidak ada topik pada filter ini</h4>
                    <p class="mt-2 text-sm text-slate-500">Ubah filter untuk melihat diskusi lainnya.</p>
                </div>
            @endforelse
        </div>

        @if ($topics->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                {{ $topics->onEachSide(1)->links() }}
            </div>
        @endif
    </section>
</x-admin-layout>
