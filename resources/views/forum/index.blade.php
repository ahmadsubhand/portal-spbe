<x-app-layout>
    <div x-data="{ filtersOpen: false, sidebarVisible: true }" x-on:keydown.escape.window="filtersOpen = false" class="min-h-screen bg-white">
        <div class="mx-auto max-w-[1500px] px-4 py-7 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-3 text-sm text-slate-500">
                <a href="{{ route('beranda') }}" class="transition hover:text-slate-800">Beranda</a><span aria-hidden="true">&rsaquo;</span><span class="font-semibold text-slate-800">Forum Diskusi</span>
            </nav>

            <form method="GET" action="{{ route('forum.index') }}" class="mt-7">
                <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Komunitas SPBE</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-slate-950">Forum Diskusi</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Tanyakan kendala, bagikan praktik baik, dan temukan pembahasan lintas unit kerja.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" x-on:click="sidebarVisible = !sidebarVisible" class="hidden rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 lg:inline-flex" x-text="sidebarVisible ? 'Sembunyikan filter' : 'Tampilkan filter'"></button>
                        @auth
                            <a href="{{ route('forum.create') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary);">Buat Diskusi</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);">Masuk untuk Berdiskusi</a>
                        @endauth
                    </div>
                </div>

                <div class="mt-6 flex gap-8">
                    <div x-cloak x-show="filtersOpen" x-on:click="filtersOpen = false" class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden"></div>
                    <aside x-cloak x-show="filtersOpen || sidebarVisible" x-transition class="fixed inset-y-0 left-0 z-50 w-[min(21rem,90vw)] overflow-y-auto border-r border-slate-200 bg-white p-5 shadow-2xl lg:static lg:z-auto lg:w-72 lg:flex-none lg:pr-7 lg:shadow-none">
                        <div class="flex items-start justify-between gap-4">
                            <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Filter Diskusi</p><p class="mt-1 text-xs leading-5 text-slate-400">Gabungkan beberapa pilihan.</p></div>
                            <button type="button" x-on:click="filtersOpen = false" class="rounded-lg bg-slate-100 p-2 text-slate-600 lg:hidden">✕</button>
                        </div>

                        <fieldset class="mt-7">
                            <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Status</legend>
                            <div class="mt-3 space-y-1">
                                @foreach ([['answered', 'Sudah dijawab'], ['unanswered', 'Belum dijawab']] as [$value, $label])
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 hover:bg-slate-50">
                                        <span class="flex items-center gap-3 text-sm text-slate-700"><input type="checkbox" name="status[]" value="{{ $value }}" @checked(in_array($value, $filters['statuses'], true)) class="rounded border-slate-300 text-blue-600">{{ $label }}</span>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $statusCounts[$value] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset class="mt-7 border-t border-slate-200 pt-6">
                            <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Format</legend>
                            <div class="mt-3 space-y-1">
                                @foreach ([['text', 'Teks'], ['image', 'Gambar'], ['video', 'Video / Multimedia']] as [$value, $label])
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 hover:bg-slate-50">
                                        <span class="flex items-center gap-3 text-sm text-slate-700"><input type="checkbox" name="format[]" value="{{ $value }}" @checked(in_array($value, $filters['formats'], true)) class="rounded border-slate-300 text-blue-600">{{ $label }}</span>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $formatCounts[$value] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset class="mt-7 border-t border-slate-200 pt-6">
                            <legend class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Kategori</legend>
                            <div class="mt-3 max-h-72 space-y-1 overflow-y-auto pr-1">
                                @foreach ($categoryCounts as $category)
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 hover:bg-slate-50">
                                        <span class="flex min-w-0 items-center gap-3 text-sm text-slate-700"><input type="checkbox" name="category[]" value="{{ $category->category }}" @checked(in_array($category->category, $filters['categories'], true)) class="rounded border-slate-300 text-blue-600"><span class="truncate">{{ $category->category }}</span></span>
                                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $category->total }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="sticky bottom-0 mt-7 grid grid-cols-2 gap-2 border-t border-slate-200 bg-white pt-4">
                            <a href="{{ route('forum.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700">Reset</a>
                            <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Terapkan</button>
                        </div>
                    </aside>

                    <section class="min-w-0 flex-1">
                        <div class="flex flex-col gap-3 md:flex-row">
                            <button type="button" x-on:click="filtersOpen = true" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 lg:hidden">Filter</button>
                            <div class="flex min-w-0 flex-1 rounded-xl border border-slate-200 shadow-sm focus-within:border-blue-400">
                                <span class="flex items-center pl-4 text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.5-4.5m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg></span>
                                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari topik, isi, kategori, atau penulis" class="min-w-0 flex-1 border-0 px-3 py-3 text-sm focus:ring-0">
                                <button type="submit" class="rounded-r-xl px-5 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Cari</button>
                            </div>
                            <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm md:w-48">
                                <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                                <option value="active" @selected($filters['sort'] === 'active')>Aktif Dibalas</option>
                                <option value="popular" @selected($filters['sort'] === 'popular')>Paling Dilihat</option>
                            </select>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3"><p class="text-sm text-slate-500"><strong class="text-slate-900">{{ $topics->total() }}</strong> diskusi ditemukan</p>@if($filters['search'] !== '' || $filters['categories'] !== [] || $filters['statuses'] !== [] || $filters['formats'] !== [])<a href="{{ route('forum.index') }}" class="text-xs font-bold text-blue-700 hover:underline">Hapus semua filter</a>@endif</div>

                        <ul class="mt-6 list-none space-y-4 p-0">
                            @forelse ($topics as $topic)
                                @php
                                    $hasVideo = str_contains($topic->content, '<iframe');
                                    $hasImage = str_contains($topic->content, '<img');
                                    $formatLabel = $hasVideo ? 'Multimedia' : ($hasImage ? 'Gambar' : 'Teks');
                                @endphp
                                <li>
                                    <a href="{{ route('forum.show', $topic->slug) }}" class="group block rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 sm:p-6">
                                        <div class="flex gap-4">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xs font-extrabold text-blue-700">{{ strtoupper(\Illuminate\Support\Str::of($topic->user->name ?? 'AN')->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('')) }}</div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2 text-xs"><span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">{{ $topic->category }}</span><span class="rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-600">{{ $formatLabel }}</span><span class="rounded-full px-2.5 py-1 font-semibold {{ $topic->isOpen() ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-800 text-white' }}">{{ $topic->isOpen() ? 'Terbuka' : 'Tertutup' }}</span>@if($topic->replies_count === 0)<span class="rounded-full bg-amber-50 px-2.5 py-1 font-semibold text-amber-700">Belum dijawab</span>@endif</div>
                                                <h2 class="mt-3 text-xl font-bold leading-snug text-slate-900 transition group-hover:text-blue-700">{{ $topic->title }}</h2>
                                                @if ($topic->isOpen() || auth()->check())
                                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($topic->content), 190) }}</p>
                                                @else
                                                    <p class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-slate-400"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M8 11V8a4 4 0 1 1 8 0v3m-9 0h10v9H7v-9Z"/></svg>Masuk untuk melihat isi diskusi tertutup.</p>
                                                @endif
                                                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500"><span class="font-semibold text-slate-700">{{ $topic->user->name ?? 'Anonim' }}</span><span>{{ $topic->created_at->diffForHumans() }}</span><span>{{ $topic->replies_count }} balasan</span><span>{{ $topic->views_count }} dilihat</span><span>{{ $topic->likes_count }} suka</span></div>
                                            </div>
                                            <svg class="mt-2 hidden h-5 w-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 p-12 text-center text-sm text-slate-500">Belum ada diskusi yang cocok dengan filter saat ini.</li>
                            @endforelse
                        </ul>

                        @if ($topics->hasPages())<div class="mt-8">{{ $topics->links() }}</div>@endif
                    </section>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
