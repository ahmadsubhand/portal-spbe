@php
    $inputName = $inputName ?? 'konten';
    $previewType = $previewType ?? 'article';
@endphp
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="block text-sm font-bold text-slate-700">Konten</label>
        <div class="inline-flex rounded-xl border border-slate-200 bg-white p-1 text-sm font-semibold text-slate-600">
            <button type="button" @click="mode = 'edit'" class="rounded-lg px-4 py-2 transition" :class="mode === 'edit' ? 'bg-slate-100 text-slate-900' : ''">Tulis</button>
            <button type="button" @click="mode = 'preview'" class="rounded-lg px-4 py-2 transition" :class="mode === 'preview' ? 'bg-slate-100 text-slate-900' : ''">Preview</button>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div x-show="mode === 'edit'" x-cloak class="p-4">
            <div
                class="space-y-3"
                data-quill-wrapper
                data-upload-url="{{ $uploadUrl }}"
                data-csrf-token="{{ csrf_token() }}"
            >
                <div data-upload-status class="hidden rounded-xl px-4 py-3 text-xs font-semibold" role="status" aria-live="polite"></div>
                <div class="rounded-t-xl border border-slate-200 border-b-0 bg-slate-50 p-2" data-quill-toolbar>
                    <span class="ql-formats">
                        <select class="ql-header">
                            <option selected></option>
                            <option value="2"></option>
                            <option value="3"></option>
                            <option value="4"></option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-blockquote"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link"></button>
                        <button class="ql-image"></button>
                        <button class="ql-video"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean"></button>
                    </span>
                </div>

                <div class="overflow-hidden rounded-b-xl border border-slate-200 bg-white">
                    <div data-quill-editor class="min-h-[420px] bg-white"></div>
                </div>

                <textarea
                    id="{{ $inputName }}"
                    name="{{ $inputName }}"
                    x-model="content"
                    data-quill-input
                    class="hidden"
                    required
                >{{ $content }}</textarea>
            </div>
        </div>

        <div x-show="mode === 'preview'" x-cloak class="p-4">
            <div class="mx-auto max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 bg-gradient-to-br from-sky-50 via-white to-white px-5 py-7 sm:px-8 sm:py-10">
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold" style="color: var(--portal-primary);">
                    <span x-text="categoryName || '{{ $previewType === 'forum' ? 'Diskusi SPBE' : 'Artikel SPBE' }}'"></span>
                </span>

                <h2 class="mt-5 text-3xl font-extrabold leading-tight text-slate-950 sm:text-4xl" x-text="title || '{{ $previewType === 'forum' ? 'Judul diskusi akan tampil di sini' : 'Judul artikel akan tampil di sini' }}'"></h2>

                <div class="mt-5 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                    <div class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3v3m8-3v3M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/></svg>
                        <span>Diperbarui <span x-text="todayLabel"></span></span>
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0"/></svg>
                        <span>Dibuat oleh <span x-text="authorName"></span></span>
                    </div>
                    <template x-if="selectedTagNames.length">
                        <div class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m20 13-7 7-9-9V4h7l9 9Z"/><path stroke-linecap="round" stroke-width="1.8" d="M8 8h.01"/></svg>
                            <span x-text="selectedTagNames.join(', ')"></span>
                        </div>
                    </template>
                </div>
                </header>

                <div class="space-y-10 px-5 py-7 sm:px-8 sm:py-10">
                <section @class([
                    'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8' => $previewType === 'article',
                ])>
                    <div class="prose prose-slate max-w-none text-[1rem] leading-8" x-html="previewHtml"></div>
                </section>
                @if ($previewType === 'article')
                <section class="border-t border-slate-200 pt-8">
                    <h3 class="text-2xl font-extrabold text-slate-900">Meta Data</h3>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Kategori</p>
                            <p class="mt-2 text-base font-semibold text-slate-900" x-text="categoryName || 'Artikel SPBE'"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Penulis</p>
                            <p class="mt-2 text-base font-semibold text-slate-900" x-text="authorName"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 md:col-span-2">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Label</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <template x-for="tagName in selectedTagNames" :key="tagName">
                                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600" x-text="tagName"></span>
                                </template>
                                <template x-if="!selectedTagNames.length">
                                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600">Publikasi SPBE</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
                </div>
            </div>
        </div>
    </div>

    <div data-image-modal class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Media</p><h3 class="mt-2 text-xl font-extrabold text-slate-950">Atur ukuran gambar</h3><p class="mt-2 text-sm text-slate-500">Ukuran persentase mengikuti lebar area tulisan.</p></div><button type="button" data-image-modal-close class="rounded-xl bg-slate-100 px-3 py-2 text-slate-600">✕</button></div>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" data-image-size="original" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Original</button>
                @foreach ([25, 50, 75, 100] as $size)<button type="button" data-image-size="{{ $size }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">{{ $size }}%</button>@endforeach
            </div>
        </div>
    </div>

    <p class="text-xs text-slate-500">
        Gambar JPG, PNG, GIF, atau WebP maksimal 5 MB dapat dipilih, ditempel, atau diseret ke editor. Klik gambar untuk mengatur ukurannya.
    </p>
    @error($inputName)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
