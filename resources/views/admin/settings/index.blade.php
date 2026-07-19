<x-admin-layout title="Pengaturan Portal" subtitle="Kelola informasi publik, halaman Dashboard, dan Basis Pengetahuan dari satu halaman.">
    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-bold">Pengaturan belum dapat disimpan.</p>
            <p class="mt-1">{{ $errors->first() }}</p>
        </div>
    @endif

    @unless ($settingsAvailable)
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            Tabel pengaturan belum tersedia. Jalankan migrasi database sebelum menyimpan konfigurasi.
        </div>
    @endunless

    <div class="grid gap-6 2xl:grid-cols-[minmax(0,1fr)_340px]">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            @foreach ($groups as $group)
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-5 sm:px-6">
                        <h3 class="text-lg font-bold text-slate-900">{{ $group['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $group['description'] }}</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach ($group['keys'] as $key)
                            @php($setting = $settings[$key] ?? null)
                            <div class="p-5 sm:p-6">
                                <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(300px,0.9fr)] xl:items-start">
                                    <div>
                                        <label for="setting-{{ $key }}" class="block text-sm font-bold text-slate-800">{{ $definitions[$key] }}</label>
                                        <p class="mt-2 font-mono text-xs text-slate-400">{{ $key }}</p>
                                        @if ($setting?->updater)
                                            <p class="mt-2 text-xs text-slate-500">Terakhir diperbarui oleh {{ $setting->updater->name }} pada {{ $setting->updated_at?->format('d M Y H:i') }}</p>
                                        @else
                                            <p class="mt-2 text-xs text-amber-600">Belum pernah dikonfigurasi.</p>
                                        @endif
                                    </div>

                                    <input
                                        id="setting-{{ $key }}"
                                        type="{{ str_contains($key, 'email') ? 'email' : (str_contains($key, 'url') ? 'url' : 'text') }}"
                                        name="settings[{{ $key }}]"
                                        value="{{ old('settings.'.$key, $setting?->value) }}"
                                        placeholder="{{ str_contains($key, 'email') ? 'kontak@bogorkota.go.id' : (str_contains($key, 'url') ? 'https://...' : 'Masukkan nilai pengaturan') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="sticky bottom-4 z-10 flex justify-end rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur">
                <button type="submit" @disabled(! $settingsAvailable) class="rounded-xl px-5 py-3 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-50" style="background-color: var(--portal-primary);">Simpan Semua Pengaturan</button>
            </div>
        </form>

        <aside class="space-y-5">
            <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-700">Panduan</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Dampak pengaturan</h3>
                <div class="mt-4 space-y-4 text-sm leading-relaxed text-slate-600">
                    <p><strong class="text-slate-800">Informasi publik</strong> muncul pada footer dan area bantuan portal.</p>
                    <p><strong class="text-slate-800">URL Dashboard</strong> menentukan tampilan data yang muncul pada halaman Dashboard publik.</p>
                    <p><strong class="text-slate-800">Status Basis Pengetahuan</strong> menentukan informasi layanan yang dilihat pengunjung.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Prinsip perubahan</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3"><span class="font-bold text-emerald-600">01</span><span>Gunakan email layanan resmi yang aktif dipantau.</span></li>
                    <li class="flex gap-3"><span class="font-bold text-emerald-600">02</span><span>Uji URL eksternal sebelum menampilkannya ke publik.</span></li>
                    <li class="flex gap-3"><span class="font-bold text-emerald-600">03</span><span>Setiap perubahan akan dicatat pada audit log.</span></li>
                </ul>
            </section>
        </aside>
    </div>
</x-admin-layout>
