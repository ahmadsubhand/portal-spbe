<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--portal-primary-deep);">
            Dashboard Internal
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: var(--portal-bg);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="p-6 text-slate-900">
                    <p class="text-sm text-slate-500">
                        Halaman ini dipertahankan sebagai fallback internal. Akses utama pengguna diarahkan ke area kerja yang sesuai dengan role.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold text-white" style="background-color: var(--portal-primary);">
                                Buka Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('portal.artikel.index') }}" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold text-white" style="background-color: var(--portal-primary);">
                                Buka Artikel Saya
                            </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
