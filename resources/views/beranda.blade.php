<x-app-layout>
    <div class="min-h-screen bg-white">
        <section class="relative overflow-hidden border-b border-slate-200 bg-slate-950 text-white">
            <img src="{{ asset('tugu-bogor-adhit-murridwan.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-[center_58%]" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-950/60"></div>
            <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(11,79,150,.48), rgba(28,40,64,.78));"></div>
            <div class="relative mx-auto max-w-7xl px-6 py-20 text-center lg:py-28">
                <div class="mx-auto max-w-4xl">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-sky-300">Portal Pengetahuan Pemerintah Kota Bogor</p>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">Sistem Manajemen Pengetahuan SPBE</h1>
                    <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Temukan pengetahuan dan pengalaman untuk mendukung layanan pemerintahan digital.</p>

                    <form action="{{ route('artikel.index') }}" method="GET" class="mx-auto mt-9 flex max-w-2xl rounded-2xl border border-white/20 bg-white p-1.5 text-left shadow-2xl">
                        <span class="flex items-center pl-3 text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.5-4.5m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg></span>
                        <input type="search" name="search" placeholder="Cari artikel, kategori, label, atau penulis..." class="min-w-0 flex-1 border-0 px-3 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-0">
                        <button type="submit" class="rounded-xl px-5 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Cari</button>
                    </form>
                </div>
            </div>
            <p class="absolute bottom-3 right-4 text-[9px] font-medium tracking-wide text-white/55 sm:right-6">Foto: Adhit Murridwan / Unsplash</p>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-12">
            <div class="mb-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([[$jumlahArtikel, 'Artikel'], [$jumlahTopik, 'Diskusi'], [$jumlahRegulasi, 'Regulasi'], [$kategoriPopuler->count(), 'Kategori']] as [$count, $label])
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4"><p class="text-2xl font-extrabold text-slate-900">{{ $count }}</p><p class="mt-1 text-xs font-semibold text-slate-500">{{ $label }}</p></div>
                @endforeach
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Artikel', 'Panduan dan praktik baik yang telah melalui proses review.', route('artikel.index'), 'bg-blue-50 text-blue-700'],
                    ['Forum Diskusi', 'Ruang bertanya dan berbagi pengalaman antar pengguna.', route('forum.index'), 'bg-amber-50 text-amber-700'],
                    ['Basis Pengetahuan', 'Jelajahi keterkaitan topik dan sumber pengetahuan.', route('knowledge.index'), 'bg-emerald-50 text-emerald-700'],
                    ['Dashboard', 'Lihat ringkasan indikator dan informasi utama.', route('public-dashboard.index'), 'bg-sky-50 text-sky-700'],
                ] as [$title, $description, $url, $tone])
                    <a href="{{ $url }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $tone }}">{{ $title }}</span>
                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ $description }}</p>
                        <span class="mt-5 inline-flex text-xs font-bold text-blue-700 transition group-hover:translate-x-1">Buka layanan →</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="border-y border-slate-200 bg-slate-50">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div><p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Terbaru</p><h2 class="mt-2 text-3xl font-extrabold text-slate-950">Artikel pilihan terbaru</h2><p class="mt-2 text-sm text-slate-500">Pembaruan pengetahuan yang sudah tersedia di portal.</p></div>
                    <a href="{{ route('artikel.index') }}" class="text-sm font-bold text-blue-700">Lihat semua artikel →</a>
                </div>
                <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @forelse($artikelTerbaru as $artikel)
                        <a href="{{ route('artikel.show', $artikel) }}" class="group flex min-h-[275px] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">
                            <div class="flex-1 p-5">
                                <div class="flex items-center justify-between gap-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">{{ $artikel->category?->name ?? 'Artikel SPBE' }}</span><span class="text-xs text-slate-400">{{ optional($artikel->published_at)->translatedFormat('d M Y') }}</span></div>
                                <h3 class="mt-5 text-lg font-bold leading-snug text-slate-900 transition group-hover:text-blue-700">{{ $artikel->judul }}</h3>
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ Str::limit(strip_tags($artikel->konten), 145) }}</p>
                            </div>
                            <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 text-xs font-bold text-blue-700">Baca artikel →</div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-4">Belum ada artikel publik terbaru.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-8 px-6 py-12 lg:grid-cols-[minmax(0,.75fr)_minmax(0,1.25fr)]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Eksplorasi</p>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Kategori populer</h2>
                <div class="mt-6 space-y-3">
                    @forelse($kategoriPopuler as $category)
                        <a href="{{ route('artikel.index', ['category' => [$category->slug]]) }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50/50"><span>{{ $category->name }}</span><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-500">{{ $category->published_articles_count }}</span></a>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">Kategori populer belum tersedia.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Komunitas</p><h2 class="mt-2 text-3xl font-extrabold text-slate-950">Diskusi terbaru</h2></div><a href="{{ route('forum.index') }}" class="text-sm font-bold text-blue-700">Lihat forum →</a></div>
                <div class="mt-6 divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    @forelse($topikTerbaru as $topic)
                        <a href="{{ route('forum.show', $topic->slug) }}" class="group flex items-start justify-between gap-4 p-5 transition hover:bg-slate-50">
                            <div class="min-w-0"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $topic->category }}</span><h3 class="mt-3 font-bold text-slate-900 transition group-hover:text-blue-700">{{ $topic->title }}</h3><p class="mt-2 text-xs text-slate-500">{{ $topic->user?->name ?? 'Anonim' }} · {{ $topic->replies_count }} balasan · {{ $topic->views_count }} dilihat</p></div><span class="text-blue-600">→</span>
                        </a>
                    @empty
                        <p class="p-8 text-center text-sm text-slate-500">Belum ada diskusi terbaru.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="border-t border-slate-200 bg-slate-50">
            <div class="mx-auto grid max-w-7xl gap-6 px-6 py-12 lg:grid-cols-2">
                <a href="{{ route('knowledge.index') }}" class="group rounded-3xl border border-emerald-200 bg-emerald-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-700">Basis Pengetahuan</p><h2 class="mt-3 text-2xl font-extrabold text-slate-950">Jelajahi hubungan antar pengetahuan</h2><p class="mt-3 text-sm leading-7 text-slate-600">Temukan keterkaitan antar topik, dokumen, dan sumber pengetahuan dalam satu tampilan.</p><span class="mt-5 inline-flex text-sm font-bold text-emerald-800 transition group-hover:translate-x-1">Lihat Basis Pengetahuan →</span></a>
                <a href="{{ route('public-dashboard.index') }}" class="group rounded-3xl border border-blue-200 bg-blue-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-700">Dashboard</p><h2 class="mt-3 text-2xl font-extrabold text-slate-950">Pantau informasi utama secara ringkas</h2><p class="mt-3 text-sm leading-7 text-slate-600">Lihat indikator dan informasi penting untuk membantu memahami perkembangan layanan SPBE.</p><span class="mt-5 inline-flex text-sm font-bold text-blue-800 transition group-hover:translate-x-1">Buka Dashboard →</span></a>
            </div>
        </section>
    </div>
</x-app-layout>
