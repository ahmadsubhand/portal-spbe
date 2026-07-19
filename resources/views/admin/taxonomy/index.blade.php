<x-admin-layout title="Manajemen Taksonomi" subtitle="Atur kategori sebagai domain utama SPBE dan label sebagai kata kunci lintas topik untuk membantu penemuan konten.">
    <x-slot name="actions">
        <button
            type="button"
            x-data
            x-on:click="$dispatch('open-modal', 'create-category-modal')"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            Tambah Kategori
        </button>
        <button
            type="button"
            x-data
            x-on:click="$dispatch('open-modal', 'create-tag-modal')"
            class="rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm"
            style="background-color: var(--portal-primary);"
        >
            Tambah Label
        </button>
    </x-slot>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-bold">Data belum dapat disimpan.</p>
            <p class="mt-1">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-600">Total Kategori</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['categories'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Struktur utama pengelompokan artikel.</p>
        </section>
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            <p class="text-sm font-semibold text-sky-800">Kategori Digunakan</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['used_categories'] }}</p>
            <p class="mt-2 text-xs text-sky-700">Kategori yang sudah memiliki artikel.</p>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-600">Total Label</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['tags'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Kata kunci lintas kategori untuk pencarian.</p>
        </section>
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-sm font-semibold text-emerald-800">Label Digunakan</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['used_tags'] }}</p>
            <p class="mt-2 text-xs text-emerald-700">Label yang sudah terhubung ke artikel.</p>
        </section>
    </div>

    <div class="mt-6 grid gap-6 2xl:grid-cols-[1.15fr_0.85fr]">
        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M4.5 12h15M4.5 17.25h15" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Kategori Artikel</h3>
                                <p class="mt-1 text-sm text-slate-500">Gunakan kategori domain SPBE yang jelas dan tidak tumpang tindih.</p>
                            </div>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.taxonomy.index') }}" class="flex w-full gap-2 lg:max-w-sm">
                        <input type="hidden" name="tag_q" value="{{ $filters['tag_q'] }}">
                        <input name="category_q" type="search" value="{{ $filters['category_q'] }}" placeholder="Cari kategori..." class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm">
                        <button type="submit" class="rounded-xl px-4 py-2.5 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Cari</button>
                    </form>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <article class="p-5 transition hover:bg-slate-50/70 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-slate-900">{{ $category->name }}</h4>
                                    <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">{{ $category->artikels_count }} artikel</span>
                                    @if ($category->artikels_count === 0)
                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Belum digunakan</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $category->description ?: 'Belum ada deskripsi kategori.' }}</p>
                                <p class="mt-3 break-all font-mono text-xs text-slate-400">/{{ $category->slug }}</p>
                            </div>
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                class="flex-shrink-0 rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
                            >
                                Edit
                            </button>
                        </div>
                    </article>

                    <x-modal name="edit-category-{{ $category->id }}" focusable maxWidth="lg">
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Edit Kategori</p>
                            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-slate-500">Slug akan diperbarui otomatis mengikuti nama kategori.</p>

                            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mt-6 space-y-4">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Nama kategori</label>
                                    <input name="name" type="text" value="{{ $category->name }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi</label>
                                    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">{{ $category->description }}</textarea>
                                </div>
                                <div class="flex flex-wrap justify-end gap-3">
                                    <button type="button" x-on:click="$dispatch('close-modal', 'edit-category-{{ $category->id }}')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Simpan Kategori</button>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @empty
                    <div class="p-10 text-center text-sm text-slate-500">Tidak ada kategori yang cocok dengan pencarian.</div>
                @endforelse
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                    {{ $categories->onEachSide(1)->links() }}
                </div>
            @endif
        </section>

        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5 sm:p-6">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 12.75-7.5 7.5a1.5 1.5 0 0 1-2.121 0l-6.879-6.879a1.5 1.5 0 0 1 0-2.121l7.5-7.5h5.379a1.5 1.5 0 0 1 1.06.439l4.122 4.122a1.5 1.5 0 0 1 .439 1.06v3.379Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 8.25h.008v.008h-.008V8.25Z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Label Artikel</h3>
                            <p class="mt-1 text-sm text-slate-500">Gunakan label spesifik yang dapat diterapkan lintas kategori.</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.taxonomy.index') }}" class="flex w-full gap-2">
                        <input type="hidden" name="category_q" value="{{ $filters['category_q'] }}">
                        <input name="tag_q" type="search" value="{{ $filters['tag_q'] }}" placeholder="Cari label..." class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm">
                        <button type="submit" class="rounded-xl px-4 py-2.5 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Cari</button>
                    </form>
                </div>
            </div>

            <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6 2xl:grid-cols-1">
                @forelse ($tags as $tag)
                    <article class="rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-slate-900">#{{ $tag->name }}</h4>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $tag->artikels_count }}</span>
                                </div>
                                <p class="mt-2 break-all font-mono text-xs text-slate-400">{{ $tag->slug }}</p>
                            </div>
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('open-modal', 'edit-tag-{{ $tag->id }}')"
                                class="rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-200"
                            >
                                Edit
                            </button>
                        </div>
                    </article>

                    <x-modal name="edit-tag-{{ $tag->id }}" focusable maxWidth="md">
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Edit Label</p>
                            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">#{{ $tag->name }}</h3>
                            <p class="mt-2 text-sm text-slate-500">Gunakan nama singkat dan spesifik agar mudah diterapkan lintas kategori.</p>

                            <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="mt-6 space-y-4">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Nama label</label>
                                    <input name="name" type="text" value="{{ $tag->name }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                                </div>
                                <div class="flex flex-wrap justify-end gap-3">
                                    <button type="button" x-on:click="$dispatch('close-modal', 'edit-tag-{{ $tag->id }}')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Simpan Label</button>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500 sm:col-span-2 2xl:col-span-1">Tidak ada label yang cocok dengan pencarian.</div>
                @endforelse
            </div>

            @if ($tags->hasPages())
                <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                    {{ $tags->onEachSide(1)->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-modal name="create-category-modal" focusable maxWidth="lg">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Kategori Baru</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Tambah Kategori</h3>
            <p class="mt-2 text-sm text-slate-500">Kategori sebaiknya mewakili kelompok besar konten dan memiliki deskripsi yang jelas.</p>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Nama kategori</label>
                    <input name="name" type="text" placeholder="Contoh: Keamanan SPBE" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi</label>
                    <textarea name="description" rows="4" placeholder="Jelaskan cakupan kategori ini." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></textarea>
                </div>
                <div class="flex flex-wrap justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'create-category-modal')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="create-tag-modal" focusable maxWidth="md">
        <div class="p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Label Baru</p>
            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Tambah Label</h3>
            <p class="mt-2 text-sm text-slate-500">Label sebaiknya berupa kata kunci singkat yang dapat digunakan lintas kategori dan artikel.</p>

            <form method="POST" action="{{ route('admin.tags.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Nama label</label>
                    <input name="name" type="text" placeholder="Contoh: Audit Internal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div class="flex flex-wrap justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'create-tag-modal')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Tambah Label</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-admin-layout>
