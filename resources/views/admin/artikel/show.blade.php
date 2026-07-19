@php
    $statusLabel = $artikel->is_draft
        ? 'Draft internal'
        : ($artikel->isPublished()
            ? 'Dipublikasikan'
            : ($artikel->status === \App\Models\Artikel::STATUS_PENDING
                ? 'Menunggu review'
                : ($artikel->status === \App\Models\Artikel::STATUS_APPROVED ? 'Siap dipublikasikan' : 'Ditolak')));
    $statusClass = $artikel->is_draft
        ? 'bg-slate-100 text-slate-700'
        : ($artikel->isPublished()
            ? 'bg-emerald-100 text-emerald-700'
            : ($artikel->status === \App\Models\Artikel::STATUS_PENDING
                ? 'bg-amber-100 text-amber-700'
                : ($artikel->status === \App\Models\Artikel::STATUS_APPROVED ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700')));
    $isPendingReview = ! $artikel->is_draft && $artikel->status === \App\Models\Artikel::STATUS_PENDING;
    $isReadyToPublish = ! $artikel->is_draft && $artikel->status === \App\Models\Artikel::STATUS_APPROVED && ! $artikel->isPublished();
@endphp

<x-admin-layout title="Review Lengkap Artikel" subtitle="Baca seluruh isi dan metadata artikel sebelum memberikan keputusan editorial.">
    <x-slot name="actions">
        <a href="{{ route('admin.artikel.validasi') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18"/></svg>
            Kembali ke antrean
        </a>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
    @endif

    <div class="grid gap-6 2xl:grid-cols-[minmax(0,1fr)_320px]">
        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 bg-gradient-to-br from-sky-50 via-white to-white px-5 py-7 sm:px-8 sm:py-10">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $artikel->category?->name ?? 'Tanpa kategori' }}</span>
                </div>

                <h1 class="mt-5 text-3xl font-extrabold leading-tight text-slate-950 sm:text-4xl">{{ $artikel->judul }}</h1>

                <div class="mt-5 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3v3m8-3v3M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/></svg>
                        Diperbarui {{ $artikel->updated_at?->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0"/></svg>
                        {{ $artikel->user?->name ?? 'Penulis tidak diketahui' }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $estimatedReadMinutes }} menit dibaca
                    </span>
                </div>
            </header>

            <div class="space-y-9 px-5 py-7 sm:px-8 sm:py-10">
                <section id="content">
                    <div class="prose prose-slate max-w-none break-words text-[1rem] leading-8">
                        {!! $artikel->konten !!}
                    </div>
                </section>

                <section class="border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-extrabold text-slate-900">Taksonomi</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Kategori</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ $artikel->category?->name ?? 'Tanpa kategori' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tag</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse ($artikel->tags as $tag)
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">#{{ $tag->name }}</span>
                                @empty
                                    <span class="text-sm text-slate-500">Tidak ada tag</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </article>

        <aside class="min-w-0">
            <div class="space-y-5 2xl:sticky 2xl:top-24">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Keputusan Editorial</p>
                    <h2 class="mt-2 text-lg font-extrabold text-slate-900">{{ $statusLabel }}</h2>

                    @if ($isPendingReview)
                        <p class="mt-2 text-sm leading-6 text-slate-500">Pastikan substansi, media, kategori, dan tag sudah sesuai sebelum mengambil keputusan.</p>
                        <div class="mt-5 grid gap-3">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'approve-article')" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Setujui artikel</button>
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'reject-article')" class="rounded-xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Tolak dan beri catatan</button>
                        </div>
                    @elseif ($isReadyToPublish)
                        <p class="mt-2 text-sm leading-6 text-slate-500">Artikel telah disetujui dan siap ditampilkan kepada publik.</p>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'publish-article')" class="mt-5 w-full rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Publikasikan artikel</button>
                    @elseif ($artikel->isPublished())
                        <p class="mt-2 text-sm leading-6 text-slate-500">Artikel ini sudah tersedia di portal publik.</p>
                        <a href="{{ route('artikel.show', $artikel) }}" class="mt-5 block rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50">Buka halaman publik</a>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'unpublish-article')" class="mt-3 w-full rounded-xl border border-amber-200 px-4 py-3 text-sm font-bold text-amber-700 transition hover:bg-amber-50">Nonaktifkan artikel</button>
                    @elseif ($artikel->review_note)
                        <p class="mt-2 text-sm leading-6 text-slate-500">Artikel telah dikembalikan kepada penulis untuk diperbaiki.</p>
                    @else
                        <p class="mt-2 text-sm leading-6 text-slate-500">Draft hanya dapat dibaca. Keputusan tersedia setelah penulis mengajukan review.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-rose-600">Zona Berbahaya</p>
                    <p class="mt-2 text-sm leading-6 text-rose-800">Penghapusan bersifat permanen. Gunakan nonaktifkan untuk menyimpan riwayat artikel.</p>
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'delete-article')" class="mt-4 w-full rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white">Hapus permanen</button>
                </section>

                @if ($artikel->review_note)
                    <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-rose-600">Catatan Review</p>
                        <p class="mt-3 text-sm leading-6 text-rose-800">{{ $artikel->review_note }}</p>
                    </section>
                @endif

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Riwayat Status</p>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Dibuat</dt><dd class="text-right font-semibold text-slate-700">{{ $artikel->created_at?->format('d M Y H:i') }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Diajukan</dt><dd class="text-right font-semibold text-slate-700">{{ $artikel->submitted_at?->format('d M Y H:i') ?? 'Belum' }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Disetujui</dt><dd class="text-right font-semibold text-slate-700">{{ $artikel->approved_at?->format('d M Y H:i') ?? 'Belum' }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Dipublikasi</dt><dd class="text-right font-semibold text-slate-700">{{ $artikel->published_at?->format('d M Y H:i') ?? 'Belum' }}</dd></div>
                    </dl>
                </section>
            </div>
        </aside>
    </div>

    <x-modal name="approve-article" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Konfirmasi Persetujuan</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Setujui artikel ini?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Artikel akan masuk ke tahap siap dipublikasikan. Pastikan seluruh konten sudah diperiksa.</p>
            <form method="POST" action="{{ route('admin.artikel.approve', $artikel) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                <button type="button" x-on:click="$dispatch('close-modal', 'approve-article')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Ya, Setujui</button>
            </form>
        </div>
    </x-modal>

    <x-modal name="reject-article" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600">Kembalikan Artikel</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Berikan catatan perbaikan</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Tulis alasan yang spesifik dan dapat ditindaklanjuti oleh penulis.</p>
            <form method="POST" action="{{ route('admin.artikel.reject', $artikel) }}" class="mt-6 space-y-4">
                @csrf
                <textarea name="review_note" rows="6" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Contoh: perjelas sumber data pada bagian..., perbaiki kategori..., dan..." required></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'reject-article')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white">Kirim Catatan</button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="publish-article" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Konfirmasi Publikasi</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Publikasikan ke portal publik?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Setelah dipublikasikan, artikel dapat dibaca oleh pengunjung portal.</p>
            <form method="POST" action="{{ route('admin.artikel.publish', $artikel) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                <button type="button" x-on:click="$dispatch('close-modal', 'publish-article')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white">Ya, Publikasikan</button>
            </form>
        </div>
    </x-modal>

    <x-modal name="unpublish-article" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Nonaktifkan Artikel</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Tarik artikel dari portal publik?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Artikel tetap tersimpan dan berstatus disetujui, sehingga dapat dipublikasikan kembali.</p>
            <form method="POST" action="{{ route('admin.artikel.unpublish', $artikel) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                <button type="button" x-on:click="$dispatch('close-modal', 'unpublish-article')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-amber-600 px-4 py-3 text-sm font-bold text-white">Ya, Nonaktifkan</button>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-article" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600">Hapus Permanen</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Hapus artikel ini?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Artikel dan relasi tag-nya akan dihapus dan tidak dapat dipulihkan.</p>
            <form method="POST" action="{{ route('admin.artikel.destroy', $artikel) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" x-on:click="$dispatch('close-modal', 'delete-article')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white">Ya, Hapus Permanen</button>
            </form>
        </div>
    </x-modal>
</x-admin-layout>
