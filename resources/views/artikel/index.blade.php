<x-app-layout>
    <div x-data="{ filtersOpen: false, sidebarVisible: true }" x-on:keydown.escape.window="filtersOpen = false" class="min-h-screen bg-white">
        <div class="mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-3 text-sm text-slate-500">
                <a href="{{ route('beranda') }}" class="transition hover:text-slate-800">Beranda</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span class="font-semibold text-slate-800">Artikel</span>
            </nav>

            <form method="GET" action="{{ route('artikel.index') }}" class="mt-7">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Basis Pengetahuan</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-slate-950">Artikel Publik SPBE</h1>
                    </div>
                    <button type="button" x-on:click="sidebarVisible = !sidebarVisible" class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 lg:inline-flex">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M4 12h16M4 19h16"/></svg>
                        <span x-text="sidebarVisible ? 'Sembunyikan filter' : 'Tampilkan filter'"></span>
                    </button>
                </div>

                <div class="mt-6 flex gap-8">
                    <div x-cloak x-show="filtersOpen" x-on:click="filtersOpen = false" class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden"></div>
                    <aside
                        x-cloak
                        x-show="filtersOpen || sidebarVisible"
                        x-transition
                        @class([
                            'fixed inset-y-0 left-0 z-50 w-[min(21rem,90vw)] overflow-y-auto border-r border-slate-200 bg-white p-5 shadow-2xl',
                            'lg:static lg:z-auto lg:w-72 lg:flex-none lg:border-r lg:pr-7 lg:shadow-none',
                        ])
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Filter Artikel</p>
                                <p class="mt-1 text-sm text-slate-500">Pilih beberapa filter sekaligus.</p>
                            </div>
                            <button type="button" x-on:click="filtersOpen = false" class="rounded-lg bg-slate-100 p-2 text-slate-600 lg:hidden" aria-label="Tutup filter">✕</button>
                        </div>

                        <fieldset class="mt-7">
                            <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Format</legend>
                            <div class="mt-3 space-y-2">
                                @foreach ([
                                    ['text', 'Teks', 'document'],
                                    ['image', 'Gambar', 'image'],
                                    ['video', 'Video / Multimedia', 'video'],
                                ] as [$value, $label, $icon])
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 transition hover:bg-slate-50">
                                        <span class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                                            <input type="checkbox" name="format[]" value="{{ $value }}" @checked(in_array($value, $filters['formats'], true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            {{ $label }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $formatCounts[$value] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset class="mt-7 border-t border-slate-200 pt-6">
                            <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Kategori</legend>
                            <div class="mt-3 max-h-72 space-y-1 overflow-y-auto pr-1">
                                @foreach ($categories as $category)
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 transition hover:bg-slate-50">
                                        <span class="flex min-w-0 items-center gap-3 text-sm text-slate-700">
                                            <input type="checkbox" name="category[]" value="{{ $category->slug }}" @checked(in_array($category->slug, $filters['categories'], true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="truncate">{{ $category->name }}</span>
                                        </span>
                                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $category->published_articles_count }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        @if ($tags->isNotEmpty())
                            <fieldset class="mt-7 border-t border-slate-200 pt-6">
                                <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Label Topik</legend>
                                <div class="mt-3 max-h-64 space-y-1 overflow-y-auto pr-1">
                                    @foreach ($tags as $tag)
                                        <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 transition hover:bg-slate-50">
                                            <span class="flex min-w-0 items-center gap-3 text-sm text-slate-700">
                                                <input type="checkbox" name="tag[]" value="{{ $tag->slug }}" @checked(in_array($tag->slug, $filters['tags'], true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <span class="truncate">{{ $tag->name }}</span>
                                            </span>
                                            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $tag->published_articles_count }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        <div class="sticky bottom-0 mt-7 grid grid-cols-2 gap-2 border-t border-slate-200 bg-white pt-4">
                            <a href="{{ route('artikel.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700">Reset</a>
                            <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Terapkan</button>
                        </div>
                    </aside>

                    <section class="min-w-0 flex-1">
                        <div class="flex flex-col gap-3 md:flex-row">
                            <button type="button" x-on:click="filtersOpen = true" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 lg:hidden">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10m-7 6h4"/></svg>
                                Filter
                            </button>
                            <div class="flex min-w-0 flex-1 rounded-xl border border-slate-200 bg-white shadow-sm focus-within:border-blue-400">
                                <span class="flex items-center pl-4 text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari artikel, kategori, label, atau penulis" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-3 text-sm focus:ring-0">
                                <button type="submit" class="rounded-r-xl px-5 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Cari</button>
                            </div>
                            <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm md:w-44">
                                <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                                <option value="oldest" @selected($filters['sort'] === 'oldest')>Terlama</option>
                            </select>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-sm text-slate-500"><strong class="text-slate-900">{{ $artikels->total() }}</strong> artikel ditemukan</p>
                            @if ($filters['search'] !== '' || $filters['categories'] !== [] || $filters['tags'] !== [] || $filters['formats'] !== [])
                                <a href="{{ route('artikel.index') }}" class="text-xs font-bold text-blue-700 hover:underline">Hapus semua filter</a>
                            @endif
                        </div>

                        <ul class="mt-6 grid list-none gap-5 p-0 md:grid-cols-2 2xl:grid-cols-3">
                            @forelse ($artikels as $artikel)
                                @php
                                    $hasVideo = str_contains($artikel->konten, '<iframe');
                                    $hasImage = str_contains($artikel->konten, '<img');
                                    $formatLabel = $hasVideo ? 'Multimedia' : ($hasImage ? 'Gambar' : 'Teks');
                                @endphp
                                <li>
                                    <a href="{{ route('artikel.show', $artikel) }}" class="group flex min-h-[280px] h-full flex-col overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                                        <div class="flex-1 p-6">
                                            <div class="flex items-start justify-between gap-3">
                                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600">{{ $formatLabel }}</span>
                                                <span class="inline-flex max-w-[55%] truncate rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">{{ $artikel->category?->name ?? 'Artikel SPBE' }}</span>
                                            </div>
                                            <h2 class="mt-5 text-xl font-bold leading-snug text-slate-900 transition group-hover:text-blue-700">{{ $artikel->judul }}</h2>
                                            <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                                                <span>{{ optional($artikel->published_at)->translatedFormat('d F Y') }}</span>
                                            </div>
                                            <p class="mt-4 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($artikel->konten), 145) }}</p>
                                        </div>
                                        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 transition group-hover:bg-blue-50/60">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex min-w-0 flex-wrap gap-2">
                                                    @foreach ($artikel->tags->take(2) as $tag)
                                                        <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs text-slate-500">{{ $tag->name }}</span>
                                                    @endforeach
                                                </div>
                                                <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 p-12 text-center text-sm text-slate-500 md:col-span-2 2xl:col-span-3">Tidak ada artikel yang cocok dengan filter saat ini.</li>
                            @endforelse
                        </ul>

                        @if ($artikels->hasPages())
                            <div class="mt-10">{{ $artikels->links() }}</div>
                        @endif
                    </section>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
