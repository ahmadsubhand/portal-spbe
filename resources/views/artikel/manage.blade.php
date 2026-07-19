<x-asn-layout title="Artikel Saya" subtitle="Kelola draft, pantau review, dan tindak lanjuti catatan admin.">
    <x-slot:actions><a href="{{ route('portal.artikel.create') }}" class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white">Tulis artikel</a></x-slot:actions>
    @if(session('success'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>@endif
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">@foreach([['Semua',$stats['all'],''],['Draft',$stats['draft'],'draft'],['Dalam review',$stats['review'],'review'],['Terpublikasi',$stats['published'],'published']] as $item)<a href="{{ route('portal.artikel.index',array_filter(['status'=>$item[2]])) }}" class="rounded-2xl border border-slate-200 p-4 {{ $filters['status']===$item[2] ? 'bg-sky-50 ring-1 ring-sky-300' : 'bg-white' }}"><p class="text-xs font-bold text-slate-500">{{ $item[0] }}</p><p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $item[1] }}</p></a>@endforeach</div>
    <form class="mt-5 flex flex-col gap-3 rounded-2xl bg-slate-50 p-4 sm:flex-row"><input name="search" value="{{ $filters['search'] }}" placeholder="Cari judul artikel..." class="min-w-0 flex-1 rounded-xl border-slate-200 bg-white text-sm"><select name="status" class="rounded-xl border-slate-200 bg-white text-sm"><option value="">Semua status</option>@foreach(['draft'=>'Draft','review'=>'Dalam review','approved'=>'Siap dipublikasi','published'=>'Terpublikasi','rejected'=>'Ditolak'] as $value=>$label)<option value="{{ $value }}" @selected($filters['status']===$value)>{{ $label }}</option>@endforeach</select><button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white">Terapkan</button></form>
    <div class="mt-5 space-y-3">
        @forelse($artikels as $artikel)
            @php $label=$artikel->is_draft?'Draft':($artikel->isPublished()?'Terpublikasi':($artikel->status===\App\Models\Artikel::STATUS_PENDING?'Dalam review':($artikel->status===\App\Models\Artikel::STATUS_APPROVED?'Siap dipublikasi':'Perlu revisi'))); @endphp
            <article class="rounded-2xl border border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600">{{ $label }}</span><span class="text-xs text-slate-400">{{ $artikel->updated_at->diffForHumans() }}</span></div><h2 class="mt-2 text-base font-extrabold text-slate-900">{{ $artikel->judul }}</h2><p class="mt-1 text-xs text-slate-500">{{ $artikel->category?->name ?? 'Tanpa kategori' }} · {{ $artikel->tags->pluck('name')->join(', ') ?: 'Tanpa label' }}</p>@if($artikel->review_note)<p class="mt-3 rounded-xl bg-rose-50 px-3 py-2 text-xs leading-relaxed text-rose-700"><strong>Catatan reviewer:</strong> {{ $artikel->review_note }}</p>@endif</div><div class="flex flex-wrap gap-2">@can('update',$artikel)<a href="{{ route('portal.artikel.edit',$artikel) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">Edit</a>@endcan @can('delete',$artikel)<button type="button" x-data x-on:click="$dispatch('open-modal', 'delete-draft-{{ $artikel->id }}')" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button>@endcan @can('submit',$artikel)<button type="button" x-data x-on:click="$dispatch('open-modal', 'submit-review-{{ $artikel->id }}')" class="rounded-lg bg-sky-600 px-3 py-2 text-xs font-bold text-white">Ajukan review</button>@endcan @if($artikel->isPublished())<a href="{{ route('artikel.show',$artikel) }}" class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">Lihat artikel</a>@endif</div></div></article>

            @can('submit', $artikel)
                <x-modal name="submit-review-{{ $artikel->id }}" focusable maxWidth="md">
                    <div class="p-6">
                        <p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Ajukan Review</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Sudah yakin artikelnya siap?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">Setelah diajukan, artikel masuk antrean review admin dan tidak dapat diedit atau dihapus sampai admin memberikan keputusan.</p>
                        <form method="POST" action="{{ route('portal.artikel.submit', $artikel) }}" class="mt-6 flex justify-end gap-3">
                            @csrf
                            <button type="button" x-on:click="$dispatch('close-modal', 'submit-review-{{ $artikel->id }}')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Periksa lagi</button>
                            <button type="submit" class="rounded-xl bg-sky-600 px-4 py-3 text-sm font-bold text-white">Ya, ajukan review</button>
                        </form>
                    </div>
                </x-modal>
            @endcan

            @can('delete', $artikel)
                <x-modal name="delete-draft-{{ $artikel->id }}" focusable maxWidth="md">
                    <div class="p-6">
                        <p class="text-xs font-bold uppercase tracking-[.18em] text-rose-600">Hapus Draft</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Hapus artikel ini?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">Draft akan dihapus permanen dan tindakan ini tidak dapat dibatalkan.</p>
                        <form method="POST" action="{{ route('portal.artikel.destroy', $artikel) }}" class="mt-6 flex justify-end gap-3">
                            @csrf @method('DELETE')
                            <button type="button" x-on:click="$dispatch('close-modal', 'delete-draft-{{ $artikel->id }}')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Kembali</button>
                            <button type="submit" class="rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white">Hapus draft</button>
                        </form>
                    </div>
                </x-modal>
            @endcan
        @empty<div class="rounded-2xl border border-dashed border-slate-300 px-5 py-14 text-center text-sm text-slate-500">Tidak ada artikel yang sesuai filter.</div>@endforelse
    </div>
    <div class="mt-6">{{ $artikels->links() }}</div>
</x-asn-layout>
