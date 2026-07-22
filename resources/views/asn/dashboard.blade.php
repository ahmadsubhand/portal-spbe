<x-asn-layout title="Dashboard ASN" subtitle="Pantau progres artikel dan aktivitas diskusi Anda dari satu ruang kerja.">
    <x-slot:actions>
        <a href="{{ route('portal.artikel.create') }}" class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-sky-700">Tulis artikel</a>
        <a href="{{ route('forum.create') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Buat diskusi</a>
    </x-slot:actions>

    <!-- Blok Statistik Card -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([['Draft tersimpan',$stats['drafts'],'Lanjutkan tulisan yang belum diajukan','bg-slate-50 text-slate-700'],['Dalam review',$stats['in_review'],'Menunggu keputusan admin','bg-amber-50 text-amber-800'],['Perlu revisi',$stats['needs_revision'],'Tindak lanjuti catatan reviewer','bg-rose-50 text-rose-800'],['Terpublikasi',$stats['published'],'Artikel yang sudah tayang','bg-emerald-50 text-emerald-800']] as $card)
            <div class="rounded-2xl border border-slate-200 p-5 {{ $card[3] }}">
                <p class="text-sm font-bold">{{ $card[0] }}</p>
                <p class="mt-3 text-4xl font-extrabold text-slate-950">{{ $card[1] }}</p>
                <p class="mt-2 text-xs leading-relaxed opacity-80">{{ $card[2] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Blok Artikel Terbaru & Diskusi -->
    <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_.85fr]">
        <section class="rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-slate-950">Artikel terbaru</h2>
                    <p class="mt-1 text-xs text-slate-500">Tulisan yang terakhir Anda kerjakan.</p>
                </div>
                <a href="{{ route('portal.artikel.index') }}" class="text-xs font-bold text-sky-600">Lihat semua</a>
            </div>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($recentArticles as $artikel)
                <a href="{{ $artikel->isPublished() ? route('artikel.show',$artikel) : ($artikel->isApproved() ? route('portal.artikel.index') : route('portal.artikel.edit',$artikel)) }}" class="flex items-center justify-between gap-4 py-4">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ $artikel->judul }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $artikel->category?->name ?? 'Tanpa kategori' }} · {{ $artikel->updated_at->diffForHumans() }}</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-600">{{ $artikel->is_draft ? 'Draft' : ($artikel->isPublished() ? 'Publik' : ucfirst($artikel->status)) }}</span>
                </a>
                @empty
                <p class="py-10 text-center text-sm text-slate-500">Belum ada artikel. Mulai dari draft pertama Anda.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-slate-950">Diskusi Anda</h2>
                    <p class="mt-1 text-xs text-slate-500">Respons terbaru dari komunitas.</p>
                </div>
                <a href="{{ route('forum.my-topics') }}" class="text-xs font-bold text-sky-600">Lihat semua</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($recentTopics as $topic)
                <a href="{{ route('forum.show',$topic->slug) }}" class="block rounded-xl bg-slate-50 p-4">
                    <p class="line-clamp-2 text-sm font-bold text-slate-800">{{ $topic->title }}</p>
                    <p class="mt-2 text-xs text-slate-500">{{ $topic->replies_count }} balasan · {{ $topic->views_count }} dilihat</p>
                </a>
                @empty
                <p class="rounded-xl bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">Belum ada topik diskusi.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-asn-layout>