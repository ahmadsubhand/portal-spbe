@php
    $statusLabel = $topic->isActive() ? 'Aktif' : 'Nonaktif';
    $statusClass = $topic->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
    $visibilityLabel = $topic->isOpen() ? 'Terbuka' : 'Tertutup';
@endphp

<x-admin-layout title="Detail Forum" subtitle="Periksa isi topik, balasan, dan status moderasi sebelum mengambil tindakan.">
    <x-slot name="actions">
        <a href="{{ route('admin.forum.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18"/></svg>
            Kembali ke daftar
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
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $visibilityLabel }}</span>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $topic->category }}</span>
                </div>

                <h1 class="mt-5 text-3xl font-extrabold leading-tight text-slate-950 sm:text-4xl">{{ $topic->title }}</h1>

                <div class="mt-5 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3v3m8-3v3M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/></svg>
                        Diperbarui {{ $topic->updated_at?->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0"/></svg>
                        {{ $topic->user?->name ?? 'Penulis tidak diketahui' }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $estimatedReadMinutes }} menit dibaca
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 10v10H4V10h3Zm3 10h7a2 2 0 0 0 2-1.5l1.5-6A2 2 0 0 0 18.5 10H15l1-4a2 2 0 0 0-2-2l-4 6v10Z"/></svg>
                        {{ $topic->likes->count() }} suka
                    </span>
                </div>
            </header>

            <div class="space-y-9 px-5 py-7 sm:px-8 sm:py-10">
                <section id="content">
                    <div class="prose prose-slate max-w-none break-words text-[1rem] leading-8">
                        {!! $topic->content !!}
                    </div>
                </section>

                <section class="border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-extrabold text-slate-900">Ringkasan Moderasi</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $summary }}</p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Status aktif</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ $topic->isActive() ? 'Ditampilkan di portal publik' : 'Ditarik dari portal publik' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Visibilitas</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ $visibilityLabel }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </article>

        <aside class="min-w-0">
            <div class="space-y-5 2xl:sticky 2xl:top-24">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Aksi Moderasi</p>
                    <h2 class="mt-2 text-lg font-extrabold text-slate-900">{{ $statusLabel }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan nonaktifkan untuk menarik topik dari portal publik tanpa menghapus riwayatnya.</p>
                    <div class="mt-5 grid gap-3">
                        <a href="{{ route('forum.show', $topic->slug) }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50">Buka di portal publik</a>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', '{{ $topic->isActive() ? 'deactivate-topic' : 'activate-topic' }}')" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">{{ $topic->isActive() ? 'Nonaktifkan topik' : 'Aktifkan kembali' }}</button>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'delete-topic')" class="rounded-xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Hapus permanen</button>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Statistik</p>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Balasan</dt><dd class="text-right font-semibold text-slate-700">{{ $topic->replies->sum(fn ($reply) => 1 + $reply->children->count()) }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Suka</dt><dd class="text-right font-semibold text-slate-700">{{ $topic->likes->count() }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Dilihat</dt><dd class="text-right font-semibold text-slate-700">{{ $topic->views_count }}</dd></div>
                        <div class="flex items-start justify-between gap-4"><dt class="text-slate-500">Dibuat</dt><dd class="text-right font-semibold text-slate-700">{{ $topic->created_at?->format('d M Y H:i') }}</dd></div>
                    </dl>
                </section>
            </div>
        </aside>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
            <h3 class="text-lg font-bold text-slate-900">Balasan Diskusi</h3>
            <p class="mt-1 text-sm text-slate-500">Seluruh balasan yang melekat pada topik ini.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($topic->replies as $reply)
                <article class="flex items-start gap-4 px-5 py-5 sm:px-6">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold uppercase text-slate-700">
                        {{ strtoupper(\Illuminate\Support\Str::of($reply->user->name ?? 'AN')->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('')) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-bold text-slate-900">{{ $reply->user->name ?? 'Anonim' }}</span>
                            <span class="text-sm text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $reply->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $reply->isActive() ? 'Aktif' : 'Nonaktif' }}</span>
                            <span class="text-xs font-bold text-slate-500">{{ $reply->likes->count() }} suka</span>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $reply->content }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('forum.show', $topic->slug) }}#reply-{{ $reply->id }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Lihat publik</a>
                            <form method="POST" action="{{ route('admin.forum.reply.toggle-active', $reply) }}" onsubmit="return confirm('{{ $reply->isActive() ? 'Nonaktifkan balasan ini dari portal publik?' : 'Aktifkan kembali balasan ini di portal publik?' }}')">@csrf @method('PATCH')<button class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">{{ $reply->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                            <form method="POST" action="{{ route('admin.forum.reply.destroy', $reply) }}" onsubmit="return confirm('Hapus balasan ini secara permanen?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form>
                        </div>

                        @if ($reply->children->isNotEmpty())
                            <div class="mt-5 space-y-4 border-l-2 border-slate-200 pl-4 sm:pl-6">
                                @foreach ($reply->children as $child)
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <div class="flex flex-wrap gap-2 text-xs"><strong class="text-slate-800">{{ $child->user?->name ?? 'Anonim' }}</strong><span class="text-slate-400">{{ $child->created_at->diffForHumans() }}</span><span class="rounded-full px-2 py-1 font-bold {{ $child->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $child->isActive() ? 'Aktif' : 'Nonaktif' }}</span><span class="text-slate-400">{{ $child->likes->count() }} suka</span></div>
                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $child->content }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <a href="{{ route('forum.show', $topic->slug) }}#reply-{{ $child->id }}" class="rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-bold text-slate-700 transition hover:bg-slate-100">Lihat publik</a>
                                            <form method="POST" action="{{ route('admin.forum.reply.toggle-active', $child) }}" onsubmit="return confirm('{{ $child->isActive() ? 'Nonaktifkan balasan ini dari portal publik?' : 'Aktifkan kembali balasan ini di portal publik?' }}')">@csrf @method('PATCH')<button class="rounded-lg bg-amber-50 px-3 py-2 text-[11px] font-bold text-amber-700">{{ $child->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                                            <form method="POST" action="{{ route('admin.forum.reply.destroy', $child) }}" onsubmit="return confirm('Hapus balasan ini secara permanen?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-50 px-3 py-2 text-[11px] font-bold text-rose-700">Hapus</button></form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-500">Belum ada balasan.</div>
            @endforelse
        </div>
    </section>

    <x-modal name="deactivate-topic" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Nonaktifkan Topik</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Tarik topik ini dari portal publik?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Riwayat, balasan, dan like tetap disimpan. Topik bisa diaktifkan kembali nanti.</p>
            <form method="POST" action="{{ route('admin.forum.toggle-active', $topic) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('PATCH')
                <button type="button" x-on:click="$dispatch('close-modal', 'deactivate-topic')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-amber-600 px-4 py-3 text-sm font-bold text-white">Ya, Nonaktifkan</button>
            </form>
        </div>
    </x-modal>

    <x-modal name="activate-topic" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Aktifkan Kembali</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Tampilkan topik ini lagi?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Topik akan muncul kembali di forum publik sesuai visibilitasnya.</p>
            <form method="POST" action="{{ route('admin.forum.toggle-active', $topic) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('PATCH')
                <button type="button" x-on:click="$dispatch('close-modal', 'activate-topic')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white">Ya, Aktifkan</button>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-topic" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600">Hapus Permanen</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Hapus topik ini?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-500">Semua balasan dan like akan ikut terhapus karena relasinya memakai cascade delete.</p>
            <form method="POST" action="{{ route('admin.forum.destroy', $topic) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" x-on:click="$dispatch('close-modal', 'delete-topic')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white">Ya, Hapus</button>
            </form>
        </div>
    </x-modal>
</x-admin-layout>
