<x-asn-layout title="Tulis Artikel" subtitle="Susun pengetahuan sebagai draft, lengkapi metadata, lalu ajukan untuk ditinjau admin.">
        <div class="mx-auto max-w-5xl">

            <form
                method="POST"
                action="{{ route('portal.artikel.store') }}"
                class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                x-data="articleFormPreview({
                    initialTitle: @js(old('judul', '')),
                    initialContent: @js(old('konten', '')),
                    initialCategoryId: @js((string) old('category_id', '')),
                    initialTags: @js(collect(old('tags', []))->map(fn ($tagId) => (string) $tagId)->values()->all()),
                    categories: @js($categories->map(fn ($category) => ['id' => (string) $category->id, 'name' => $category->name])->values()->all()),
                    tags: @js($tags->map(fn ($tag) => ['id' => (string) $tag->id, 'name' => $tag->name])->values()->all()),
                    authorName: @js(auth()->user()->name),
                    draftKey: @js('portal-spbe:article-draft:'.auth()->id().':new'),
                    serverUpdatedAt: null,
                })"
            >
                @csrf
                <div class="flex flex-col gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-800 sm:flex-row sm:items-center sm:justify-between">
                    <p><strong>Autosave aktif.</strong> Perubahan disimpan di browser ini. <span x-show="draftSavedLabel">Terakhir disimpan <span x-text="draftSavedLabel"></span>.</span></p>
                    <button x-show="hasLocalDraft" type="button" x-on:click="discardLocalDraft()" class="self-start font-bold text-sky-700 hover:text-sky-900 sm:self-auto">Hapus draft lokal</button>
                </div>
                <div>
                    <label for="judul" class="mb-2 block text-sm font-bold text-slate-700">Judul</label>
                    <input id="judul" name="judul" type="text" value="{{ old('judul') }}" x-model="title" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-slate-300 focus:outline-none" required>
                    @error('judul')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    @include('artikel.partials.editor', [
                        'content' => old('konten'),
                        'uploadUrl' => route('portal.artikel.media.store'),
                    ])
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Kategori</label>
                    <input type="hidden" name="category_id" x-model="categoryId">
                    <div class="relative" x-on:click.outside="categoryOpen = false">
                        <button type="button" x-on:click="categoryOpen = !categoryOpen" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm text-slate-800">
                            <span x-text="categoryName || 'Tanpa kategori'"></span><span class="text-slate-400">⌄</span>
                        </button>
                        <div x-cloak x-show="categoryOpen" x-transition class="absolute z-30 mt-2 w-full rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                            <input type="search" x-model="categorySearch" x-on:keydown.escape="categoryOpen = false" placeholder="Cari kategori..." class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm">
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <button type="button" x-on:click="selectCategory('')" class="block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100">Tanpa kategori</button>
                                <template x-for="category in filteredCategories" :key="category.id"><button type="button" x-on:click="selectCategory(category.id)" class="block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100" :class="categoryId === category.id ? 'bg-sky-50 font-bold text-sky-700' : 'text-slate-700'" x-text="category.name"></button></template>
                                <p x-show="filteredCategories.length === 0" class="px-3 py-4 text-center text-xs text-slate-500">Kategori tidak ditemukan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="mb-2 block text-sm font-bold text-slate-700">Label</span>
                    <input type="search" x-model="tagSearch" placeholder="Cari label..." class="mb-3 w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                    <div class="max-h-72 overflow-y-auto rounded-xl border border-slate-200 p-2">
                        <div class="grid gap-2 md:grid-cols-2">
                            <template x-for="tag in filteredTags" :key="tag.id"><label class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"><input type="checkbox" name="tags[]" :value="tag.id" x-model="selectedTags"><span x-text="tag.name"></span></label></template>
                        </div>
                        <p x-show="filteredTags.length === 0" class="px-3 py-5 text-center text-xs text-slate-500">Label tidak ditemukan.</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">
                        Simpan Draft
                    </button>
                </div>
            </form>
        </div>
</x-asn-layout>
