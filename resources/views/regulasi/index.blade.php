<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-7xl px-6 py-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-3 text-sm text-slate-500"><a href="{{ route('beranda') }}" class="hover:text-slate-800">Beranda</a><span>&rsaquo;</span><span class="font-semibold text-slate-800">Regulasi</span></nav>

            <header class="mt-7 rounded-3xl border border-slate-200 bg-gradient-to-br from-sky-50 via-white to-white p-7 shadow-sm sm:p-9">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-600">Dokumen Pemerintahan</p>
                <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div><h1 class="text-3xl font-extrabold text-slate-950 sm:text-4xl">Katalog Regulasi SPBE</h1><p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Telusuri dokumen regulasi berdasarkan nomor, topik, dan tahun penerbitan.</p></div>
                    <div class="rounded-2xl border border-sky-200 bg-white px-5 py-4"><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Total Dokumen</p><p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $totalRegulasi }}</p></div>
                </div>
            </header>

            <form action="{{ route('regulasi.index') }}" method="GET" class="mt-7 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_180px_180px_auto]">
                <div class="flex min-w-0 rounded-xl border border-slate-200 focus-within:border-blue-400"><span class="flex items-center pl-4 text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.5-4.5m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg></span><input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari nomor atau topik regulasi" class="min-w-0 flex-1 rounded-xl border-0 px-3 py-3 text-sm focus:ring-0"></div>
                <select name="tahun" class="rounded-xl border border-slate-200 px-4 py-3 text-sm"><option value="">Semua Tahun</option>@foreach($daftarTahun as $tahun)<option value="{{ $tahun }}" @selected((string) $tahun === $filters['tahun'])>{{ $tahun }}</option>@endforeach</select>
                <select name="sort" class="rounded-xl border border-slate-200 px-4 py-3 text-sm"><option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option><option value="oldest" @selected($filters['sort'] === 'oldest')>Terlama</option></select>
                <button type="submit" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Terapkan</button>
            </form>

            <div class="mt-5 flex items-center justify-between gap-4"><p class="text-sm text-slate-500"><strong class="text-slate-900">{{ $regulasis->total() }}</strong> regulasi ditemukan</p>@if($filters['search'] !== '' || $filters['tahun'] !== '')<a href="{{ route('regulasi.index') }}" class="text-xs font-bold text-blue-700 hover:underline">Hapus filter</a>@endif</div>

            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse($regulasis as $regulasi)
                    <article class="flex min-h-[250px] flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">
                        <div class="flex items-start justify-between gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l4 4v14H7V3Zm7 0v5h5M10 13h5m-5 4h5"/></svg></span><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $regulasi->tahun }}</span></div>
                        <div class="mt-5 flex-1"><p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-700">{{ $regulasi->nomor }}</p><h2 class="mt-3 text-lg font-bold leading-7 text-slate-900">{{ $regulasi->tentang }}</h2></div>
                        <div class="mt-6 border-t border-slate-200 pt-4">
                            @if($regulasi->file_path)
                                <a href="{{ Storage::disk('public')->url($regulasi->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-bold text-blue-700">Buka dokumen <span aria-hidden="true">↗</span></a>
                            @else
                                <span class="text-xs font-semibold text-slate-400">Berkas digital belum tersedia</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">Tidak ada regulasi yang cocok dengan filter saat ini.</div>
                @endforelse
            </div>

            @if($regulasis->hasPages())<div class="mt-9">{{ $regulasis->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
