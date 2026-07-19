<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-7xl px-6 py-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                <a href="{{ route('beranda') }}" class="hover:text-slate-700 transition">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ route('artikel.index') }}" class="hover:text-slate-700 transition">Artikel</a>
                <span>&rsaquo;</span>
                <span class="max-w-[50vw] truncate font-semibold text-slate-800">{{ $artikel->judul }}</span>
            </nav>
            <a href="{{ route('artikel.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19 3 12l7-7m-7 7h18"/></svg>
                Kembali ke Artikel
            </a>

            <div class="mt-8 grid gap-12 lg:grid-cols-[minmax(0,1fr)_260px]">
                <article>
                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold" style="color: var(--portal-primary);">
                        {{ $artikel->category?->name ?? 'Artikel SPBE' }}
                    </span>

                    <h1 class="mt-5 text-4xl font-extrabold leading-tight text-slate-950">
                        {{ $artikel->judul }}
                    </h1>

                    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                        <div class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                            </svg>
                            <span>Diperbarui {{ optional($artikel->published_at)->translatedFormat('d-m-Y') ?? $artikel->created_at->translatedFormat('d-m-Y') }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Dibuat oleh {{ $artikel->user?->name ?? 'Tim Portal' }}</span>
                        </div>
                        @if ($artikel->tags->isNotEmpty())
                            <div class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0l6.586-6.586a2 2 0 000-2.828L12.414 3H7a2 2 0 00-2 2v6z"/>
                                </svg>
                                <span>{{ $artikel->tags->pluck('name')->join(', ') }}</span>
                            </div>
                        @endif
                    </div>

                    <hr class="mt-8 border-slate-200">

                    @auth
                        <section id="content" class="mt-8">
                            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                                <div class="prose prose-slate max-w-none text-[1rem] leading-8">
                                    {!! $artikel->konten !!}
                                </div>
                            </div>
                        </section>
                    @else
                        <section id="summary" class="mt-8">
                            <h2 class="text-2xl font-extrabold text-slate-900">Ringkasan</h2>
                            <p class="mt-5 text-lg leading-9 text-slate-700">{{ $guestSummary }}</p>
                        </section>

                        <section class="mt-10 rounded-[28px] border border-slate-200 bg-slate-50 px-6 py-12 text-center sm:px-10">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-blue-700 shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11V7a4 4 0 1 1 8 0v4m-8 0H6a2 2 0 0 0-2 2v6h16v-6a2 2 0 0 0-2-2h-6Z"/></svg>
                            </div>
                            <h2 class="mt-5 text-2xl font-extrabold text-slate-900">Masuk untuk membaca artikel lengkap</h2>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-500">Konten lengkap portal pengetahuan tersedia untuk akun internal Pemerintah Kota Bogor yang telah diverifikasi.</p>
                            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                                <a href="{{ route('login') }}" class="rounded-xl px-6 py-3 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);">Masuk</a>
                                <a href="{{ route('register') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700">Minta akses</a>
                            </div>
                        </section>
                    @endauth

                    @auth
                    <section id="meta-data" class="mt-12">
                        <h2 class="text-2xl font-extrabold text-slate-900">Meta Data</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Kategori</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $artikel->category?->name ?? 'Artikel SPBE' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Penulis</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $artikel->user?->name ?? 'Tim Portal' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 md:col-span-2">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Label</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse ($artikel->tags as $tag)
                                        <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600">
                                            {{ $tag->name }}
                                        </span>
                                    @empty
                                        <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600">
                                            Publikasi SPBE
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                    @endauth
                </article>

                <aside class="lg:pt-24">
                    <div class="sticky top-24 space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="inline-flex items-center gap-2 text-sm text-slate-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $estimatedReadMinutes }} menit dibaca</span>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold text-slate-900">Di halaman ini</p>
                            <div class="mt-4 space-y-3 text-sm text-slate-500">
                                @auth
                                    <a href="#content" class="block hover:text-slate-700 transition">Isi artikel</a>
                                    <a href="#meta-data" class="block hover:text-slate-700 transition">Meta Data</a>
                                @else
                                    <a href="#summary" class="block hover:text-slate-700 transition">Ringkasan</a>
                                @endauth
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-sm font-bold text-slate-900">Lanjutkan eksplorasi</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Kembali ke daftar artikel untuk melihat topik SPBE lainnya.
                            </p>
                            <a href="{{ route('artikel.index') }}" class="mt-4 inline-flex text-sm font-semibold" style="color: var(--portal-primary);">
                                Kembali ke artikel
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
