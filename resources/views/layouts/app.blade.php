<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="font-sans antialiased bg-slate-50">
        @php($isAdminRoute = request()->routeIs('admin.*'))

        <div class="min-h-screen flex flex-col {{ $isAdminRoute ? '' : 'justify-between' }}">
            
            <div>
                @include('layouts.navigation')

                <main>
                    {{ $slot }}
                </main>
            </div>

            @unless ($isAdminRoute)
                <footer class="mt-20 w-full clear-both text-white" style="background-color: var(--portal-ink);">
                    <div class="border-b border-white/10">
                        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-8 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-300">Portal Pengetahuan SPBE</p>
                                <h2 class="mt-2 text-xl font-extrabold text-white">Temukan informasi, regulasi, dan diskusi pemerintahan digital.</h2>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('artikel.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-900 transition hover:bg-slate-100">Telusuri Artikel</a>
                                <a href="{{ route('forum.index') }}" class="rounded-xl border border-white/20 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/10">Buka Forum</a>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto max-w-7xl px-6 py-10">
                        <div class="grid gap-9 sm:grid-cols-2 lg:grid-cols-[1.35fr_0.8fr_0.8fr_1fr]">
                            <div>
                                <x-portal-brand dark size="sm" />
                                <p class="mt-4 max-w-sm text-xs leading-6 text-slate-400">Portal pengelolaan dan berbagi pengetahuan Sistem Pemerintahan Berbasis Elektronik Pemerintah Kota Bogor.</p>
                                <a href="mailto:{{ $portalSettings[\App\Models\Setting::PUBLIC_CONTACT_EMAIL] ?? 'kontak@bogorkota.go.id' }}" class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-sky-300 hover:text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 6 9 7 9-7M4 5h16v14H4V5Z"/></svg>
                                    {{ $portalSettings[\App\Models\Setting::PUBLIC_CONTACT_EMAIL] ?? 'kontak@bogorkota.go.id' }}
                                </a>
                            </div>

                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-[0.18em] text-white">Jelajahi</h3>
                                <ul class="mt-4 space-y-3 text-xs text-slate-400">
                                    <li><a href="{{ route('beranda') }}" class="transition hover:text-white">Beranda</a></li>
                                    <li><a href="{{ route('artikel.index') }}" class="transition hover:text-white">Artikel</a></li>
                                    <li><a href="{{ route('knowledge.index') }}" class="transition hover:text-white">Basis Pengetahuan</a></li>
                                    <li><a href="{{ route('regulasi.index') }}" class="transition hover:text-white">Regulasi</a></li>
                                </ul>
                            </div>

                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-[0.18em] text-white">Layanan</h3>
                                <ul class="mt-4 space-y-3 text-xs text-slate-400">
                                    <li><a href="{{ route('forum.index') }}" class="transition hover:text-white">Forum Diskusi</a></li>
                                    <li><a href="{{ route('public-dashboard.index') }}" class="transition hover:text-white">Dashboard Publik</a></li>
                                    @auth
                                        <li><a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'portal.dashboard') }}" class="transition hover:text-white">{{ auth()->user()->isAdmin() ? 'Area Admin' : 'Area ASN' }}</a></li>
                                        <li><a href="{{ route('profile.edit') }}" class="transition hover:text-white">Profil Saya</a></li>
                                    @else
                                        <li><a href="{{ route('login') }}" class="transition hover:text-white">Masuk Internal</a></li>
                                        <li><a href="{{ route('register') }}" class="transition hover:text-white">Minta Akses</a></li>
                                    @endauth
                                </ul>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-300">{{ $portalSettings[\App\Models\Setting::PUBLIC_SUPPORT_LABEL] ?? 'Layanan Bantuan SPBE' }}</p>
                                <p class="mt-3 text-xs leading-6 text-slate-400">Gunakan kanal resmi untuk bantuan akun, koreksi konten, atau pertanyaan layanan portal.</p>
                                <a href="mailto:{{ $portalSettings[\App\Models\Setting::PUBLIC_CONTACT_EMAIL] ?? 'kontak@bogorkota.go.id' }}" class="mt-4 inline-flex rounded-lg bg-white/10 px-3 py-2 text-xs font-bold text-white transition hover:bg-white/15">Hubungi Pengelola</a>
                            </div>
                        </div>

                        <div class="mt-9 flex flex-col gap-3 border-t border-white/10 pt-6 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                            <p>© {{ now()->year }} Pemerintah Kota Bogor. Portal SPBE.</p>
                            <p>Dikelola untuk mendukung layanan pemerintahan digital yang terpadu.</p>
                        </div>
                    </div>
                </footer>
            @endunless
            </div>
    </body>
</html>
