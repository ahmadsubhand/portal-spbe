<x-asn-layout title="Edit Diskusi" subtitle="Perbarui konteks, media, atau detail topik yang telah Anda buat.">
    <form
        action="{{ route('forum.update',$topic->id) }}"
        method="POST"
        class="mx-auto max-w-5xl space-y-5"
        x-data="articleFormPreview({
            initialTitle: @js(old('title', $topic->title)),
            initialContent: @js(old('content', $topic->content)),
            initialCategoryId: @js(old('category', $topic->category)),
            categories: @js(collect(['Regulasi','Teknis','Basis Pengetahuan','Umum'])->map(fn ($category) => ['id' => $category, 'name' => $category])->all()),
            authorName: @js(auth()->user()->name),
            draftKey: @js('portal-spbe:forum-draft:'.auth()->id().':'.$topic->id),
            serverUpdatedAt: @js($topic->updated_at->toISOString()),
        })"
    >
        @csrf @method('PUT')
        <div class="flex flex-col gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-800 sm:flex-row sm:items-center sm:justify-between"><p><strong>Autosave aktif.</strong> Perubahan disimpan di browser ini. <span x-show="draftSavedLabel">Terakhir disimpan <span x-text="draftSavedLabel"></span>.</span></p><button x-show="hasLocalDraft" type="button" x-on:click="discardLocalDraft()" class="self-start font-bold text-sky-700 sm:self-auto">Hapus draft lokal</button></div>
        <div class="space-y-5 rounded-2xl border border-slate-200 p-5 sm:p-6">
            <div><label class="mb-2 block text-sm font-bold">Judul diskusi</label><input name="title" value="{{ old('title',$topic->title) }}" x-model="title" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">@error('title')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div><label class="mb-2 block text-sm font-bold">Kategori</label><select name="category" x-model="categoryId" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">@foreach(['Regulasi','Teknis','Basis Pengetahuan','Umum'] as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></div>
            <fieldset><legend class="mb-2 block text-sm font-bold text-slate-700">Akses diskusi</legend><div class="grid gap-3 sm:grid-cols-2">@foreach([['open','Terbuka','Isi dan balasan dapat dibaca publik.'],['closed','Tertutup','Pengunjung harus masuk untuk membaca diskusi.']] as [$value,$label,$description])<label class="cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4"><span class="flex items-start gap-3"><input type="radio" name="visibility" value="{{ $value }}" @checked(old('visibility',$topic->visibility) === $value) class="mt-1 border-slate-300 text-blue-600"><span><strong class="block text-sm text-slate-800">{{ $label }}</strong><span class="mt-1 block text-xs leading-5 text-slate-500">{{ $description }}</span></span></span></label>@endforeach</div></fieldset>
            @include('artikel.partials.editor', ['content' => old('content',$topic->content), 'inputName' => 'content', 'previewType' => 'forum', 'uploadUrl' => route('portal.artikel.media.store')])
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('forum.my-topics') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold">Batal</a><button class="rounded-xl bg-sky-600 px-5 py-3 text-sm font-bold text-white">Simpan perubahan</button></div>
    </form>
</x-asn-layout>
