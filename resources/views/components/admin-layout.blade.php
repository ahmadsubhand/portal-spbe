@props([
    'title' => 'Admin',
    'subtitle' => null,
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Review Artikel', 'route' => 'admin.artikel.validasi', 'active' => request()->routeIs('admin.artikel.*'), 'icon' => 'document'],
        ['label' => 'Forum Diskusi', 'route' => 'admin.forum.index', 'active' => request()->routeIs('admin.forum.*'), 'icon' => 'chat'],
        ['label' => 'Taksonomi', 'route' => 'admin.taxonomy.index', 'active' => request()->routeIs('admin.taxonomy.*', 'admin.categories.*', 'admin.tags.*'), 'icon' => 'tag'],
        ['label' => 'Pengguna', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.*'), 'icon' => 'users'],
        ['label' => 'Audit Log', 'route' => 'admin.audit-logs.index', 'active' => request()->routeIs('admin.audit-logs.*'), 'icon' => 'clock'],
        ['label' => 'Pengaturan', 'route' => 'admin.settings.index', 'active' => request()->routeIs('admin.settings.*'), 'icon' => 'settings'],
    ];

    $iconMap = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9.75V21h13.5V9.75" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 21v-6.75h4.5V21" />',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h6l4.5 4.5V20.25A1.5 1.5 0 0 1 16.5 21.75h-9A1.5 1.5 0 0 1 6 20.25v-15A1.5 1.5 0 0 1 7.5 3.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75v4.5H18" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 15.75h6" />',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 6.75h9A3.75 3.75 0 0 1 20.25 10.5v4.5A3.75 3.75 0 0 1 16.5 18.75H12l-4.5 3v-3H7.5A3.75 3.75 0 0 1 3.75 15v-4.5A3.75 3.75 0 0 1 7.5 6.75Z" />',
        'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="m20.25 12.75-7.5 7.5a1.5 1.5 0 0 1-2.121 0l-6.879-6.879a1.5 1.5 0 0 1 0-2.121l7.5-7.5h5.379a1.5 1.5 0 0 1 1.06.439l4.122 4.122a1.5 1.5 0 0 1 .439 1.06v3.379Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 8.25h.008v.008h-.008V8.25Z" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 21v-1.5A3.75 3.75 0 0 0 12.75 15.75h-6A3.75 3.75 0 0 0 3 19.5V21" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 11.25A3.75 3.75 0 1 0 9.75 3.75a3.75 3.75 0 0 0 0 7.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M18 8.25A2.25 2.25 0 1 1 18 12.75" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 21v-1.5A3.75 3.75 0 0 0 18 15.862" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3.75 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h3M10.5 18h3M4.5 6h.75m13.5 0h.75M4.5 18h.75m13.5 0h.75M6.75 6a2.25 2.25 0 1 0 4.5 0 2.25 2.25 0 0 0-4.5 0Zm6 12a2.25 2.25 0 1 0 4.5 0 2.25 2.25 0 0 0-4.5 0Zm0-12h6m-15 0h3m3 12h3m3 0h6" />',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} | {{ config('app.name', 'Portal SPBE') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div
            x-data="{ mobileSidebarOpen: false }"
            x-on:keydown.escape.window="mobileSidebarOpen = false"
            class="min-h-screen bg-slate-100"
        >
            <div
                x-cloak
                x-show="mobileSidebarOpen"
                x-transition.opacity
                x-on:click="mobileSidebarOpen = false"
                class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm md:hidden"
                aria-hidden="true"
            ></div>

            <aside
                x-cloak
                x-show="mobileSidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 flex w-[min(19rem,88vw)] flex-col bg-white shadow-2xl md:hidden"
                aria-label="Navigasi admin mobile"
            >
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <img src="{{ url('/Logo%20Kota%20Bogor.png') }}" alt="Logo Kota Bogor" class="h-10 w-10 object-contain">
                            <img src="{{ asset('logo_ipb.png') }}" alt="Logo IPB" class="h-9 w-9 object-contain">
                        </div>
                        <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.24em]" style="color: var(--portal-primary);">Admin Panel</p>
                        <p class="mt-1 text-lg font-extrabold text-slate-900">Portal SPBE</p>
                        </div>
                    </div>
                    <button x-on:click="mobileSidebarOpen = false" type="button" class="rounded-xl bg-slate-100 p-2.5 text-slate-600" aria-label="Tutup sidebar">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                                'text-white shadow-sm' => $item['active'],
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $item['active'],
                            ])
                            @if ($item['active']) style="background-color: var(--portal-primary-deep);" @endif
                        >
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">{!! $iconMap[$item['icon']] !!}</svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="space-y-2 border-t border-slate-200 p-3">
                    <a href="{{ route('portal.artikel.index') }}" class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);">
                        <span>Area ASN</span>
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('beranda') }}" class="flex items-center justify-between rounded-xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">
                        <span>Portal Publik</span>
                        <span aria-hidden="true">↗</span>
                    </a>
                </div>
            </aside>

            <aside class="fixed inset-y-0 left-0 z-30 hidden w-20 flex-col border-r border-slate-200 bg-white md:flex xl:hidden" aria-label="Navigasi admin tablet">
                <a href="{{ route('admin.dashboard') }}" class="flex h-20 items-center justify-center gap-1 border-b border-slate-200" aria-label="Portal SPBE">
                    <img src="{{ url('/Logo%20Kota%20Bogor.png') }}" alt="Logo Kota Bogor" class="h-8 w-8 object-contain">
                    <img src="{{ asset('logo_ipb.png') }}" alt="Logo IPB" class="h-7 w-7 object-contain">
                </a>

                <nav class="flex-1 space-y-2 overflow-y-auto px-3 py-4">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            title="{{ $item['label'] }}"
                            aria-label="{{ $item['label'] }}"
                            @class([
                                'flex h-12 items-center justify-center rounded-xl transition',
                                'text-white shadow-sm' => $item['active'],
                                'text-slate-500 hover:bg-slate-100 hover:text-slate-900' => ! $item['active'],
                            ])
                            @if ($item['active']) style="background-color: var(--portal-primary-deep);" @endif
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">{!! $iconMap[$item['icon']] !!}</svg>
                        </a>
                    @endforeach
                </nav>

                <div class="space-y-2 p-3">
                    <a href="{{ route('portal.artikel.index') }}" title="Area ASN" aria-label="Area ASN" class="flex h-12 items-center justify-center rounded-xl text-white shadow-sm" style="background-color: var(--portal-primary-deep);">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 4.5h-6v15h15v-6m-9-3 9-9m0 0h-6m6 0v6"/></svg>
                    </a>
                    <a href="{{ route('beranda') }}" title="Portal Publik" aria-label="Portal Publik" class="flex h-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3a15 15 0 0 1 0 18m0-18a15 15 0 0 0 0 18M5 5a10 10 0 0 0 14 14M19 5A10 10 0 0 1 5 19"/></svg>
                    </a>
                </div>
            </aside>

            <aside class="fixed inset-y-0 left-0 z-30 hidden w-72 flex-col border-r border-slate-200 bg-white xl:flex" aria-label="Navigasi admin desktop">
                <div class="border-b border-slate-200 px-6 py-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <img src="{{ url('/Logo%20Kota%20Bogor.png') }}" alt="Logo Kota Bogor" class="h-11 w-11 object-contain">
                            <img src="{{ asset('logo_ipb.png') }}" alt="Logo IPB" class="h-10 w-10 object-contain">
                        </div>
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Admin Panel</p>
                            <h1 class="mt-1 text-lg font-extrabold text-slate-900">Portal SPBE</h1>
                        </div>
                    </div>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Pengelolaan konten dan operasional portal.</p>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                                'text-white shadow-sm' => $item['active'],
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $item['active'],
                            ])
                            @if ($item['active']) style="background-color: var(--portal-primary-deep);" @endif
                        >
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $item['active'] ? 'bg-white/15' : 'bg-slate-100 text-slate-500' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">{!! $iconMap[$item['icon']] !!}</svg>
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-slate-200 p-4">
                    <div class="mb-3 flex items-center gap-3 px-2">
                        @if (auth()->user()->avatar_path)
                            <img src="{{ Storage::disk('public')->url(auth()->user()->avatar_path) }}" alt="Foto profil {{ auth()->user()->name }}" class="h-9 w-9 flex-shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full text-xs font-extrabold text-white" style="background-color: var(--portal-primary-deep);">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('portal.artikel.index') }}" class="mb-2 flex items-center justify-between rounded-lg px-3 py-2.5 text-xs font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);"><span>Area ASN</span><span aria-hidden="true">→</span></a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('beranda') }}" class="rounded-lg bg-slate-100 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-slate-200">Portal Publik</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">Keluar</button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 md:pl-20 xl:pl-72">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-3">
                            <button x-on:click="mobileSidebarOpen = true" type="button" class="rounded-xl bg-slate-100 p-2.5 text-slate-700 md:hidden" aria-label="Buka sidebar">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900">{{ $title }}</p>
                                <p class="hidden truncate text-xs text-slate-500 sm:block">Administrasi Portal SPBE</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('beranda') }}" class="hidden rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 sm:block">Portal Publik</a>
                            <a href="{{ route('profile.edit') }}" class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full text-xs font-extrabold text-white" style="background-color: var(--portal-primary-deep);" title="{{ auth()->user()->name }}">
                                @if (auth()->user()->avatar_path)
                                    <img src="{{ Storage::disk('public')->url(auth()->user()->avatar_path) }}" alt="Foto profil {{ auth()->user()->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </a>
                        </div>
                    </div>
                </header>

                <main class="min-h-[calc(100vh-4rem)] p-3 sm:p-5 lg:p-6 2xl:p-8" style="background:
                    radial-gradient(circle at top left, rgba(17, 126, 219, 0.08), transparent 26%),
                    linear-gradient(180deg, #f8fafc 0%, #eef4f8 100%);">
                    <div class="mx-auto w-full max-w-[1600px] rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.24em]" style="color: var(--portal-primary);">Administrasi</p>
                                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h2>
                                @if ($subtitle)
                                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-500">{{ $subtitle }}</p>
                                @endif
                            </div>

                            @isset($actions)
                                <div class="flex flex-wrap items-center gap-3">
                                    {{ $actions }}
                                </div>
                            @endisset
                        </div>

                        <div class="pt-6">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
