@props(['title' => 'Ruang Kerja ASN', 'subtitle' => null])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'portal.dashboard', 'active' => request()->routeIs('portal.dashboard'), 'icon' => 'M4 13h6V4H4v9Zm10 7h6v-9h-6v9ZM4 20h6v-3H4v3Zm10-13h6V4h-6v3Z'],
        ['label' => 'Artikel Saya', 'route' => 'portal.artikel.index', 'active' => request()->routeIs('portal.artikel.index', 'portal.artikel.edit'), 'icon' => 'M7 3h7l4 4v14H7V3Zm7 0v5h5M10 12h5m-5 4h5'],
        ['label' => 'Tulis Artikel', 'route' => 'portal.artikel.create', 'active' => request()->routeIs('portal.artikel.create'), 'icon' => 'm14.5 5.5 4 4M4 20l1-4L16 5l4 4L9 20H4Z'],
        ['label' => 'Topik Saya', 'route' => 'forum.my-topics', 'active' => request()->routeIs('forum.my-topics', 'forum.edit'), 'icon' => 'M5 6h14v10H9l-4 4V6Zm4 4h6m-6 3h4'],
        ['label' => 'Buat Diskusi', 'route' => 'forum.create', 'active' => request()->routeIs('forum.create'), 'icon' => 'M12 5v14m-7-7h14'],
        ['label' => 'Profil', 'route' => 'profile.edit', 'active' => request()->routeIs('profile.*'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0'],
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
    <style>[x-cloak]{display:none!important}</style>
    @if (session('clear_article_draft'))
        <script>localStorage.removeItem(@js(session('clear_article_draft')));</script>
    @endif
</head>
<body class="font-sans antialiased text-slate-800">
<div x-data="{ sidebarOpen: false }" x-on:keydown.escape.window="sidebarOpen = false" class="min-h-screen bg-slate-100">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity x-on:click="sidebarOpen=false" class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm md:hidden"></div>

    <aside x-cloak x-show="sidebarOpen" x-transition class="fixed inset-y-0 left-0 z-50 flex w-[min(19rem,88vw)] flex-col bg-white text-slate-800 shadow-2xl md:hidden">
        <div class="flex items-center justify-between border-b border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <div class="flex gap-1"><img src="{{ url('/Logo%20Kota%20Bogor.png') }}" class="h-9 w-9 object-contain" alt="Kota Bogor"><img src="{{ asset('logo_ipb.png') }}" class="h-9 w-9 object-contain" alt="IPB"></div>
                <div><p class="text-[9px] font-bold uppercase tracking-[.22em] text-sky-600">Ruang Kerja ASN</p><p class="font-extrabold text-slate-950">Portal SPBE</p></div>
            </div>
            <button type="button" x-on:click="sidebarOpen=false" class="rounded-xl bg-slate-100 p-2.5 text-slate-600" aria-label="Tutup navigasi">✕</button>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" @class(['flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition', 'bg-sky-600 text-white shadow-sm' => $item['active'], 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => !$item['active']])>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="space-y-2 border-t border-slate-200 p-4">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);">
                    <span>Area Admin</span>
                    <span aria-hidden="true">→</span>
                </a>
            @endif
            <a href="{{ route('beranda') }}" class="block rounded-xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">Lihat portal publik →</a>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('profile.edit') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700">Profil</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Keluar</button></form>
            </div>
        </div>
    </aside>

    <aside class="fixed inset-y-0 left-0 z-30 hidden w-20 flex-col border-r border-slate-200 bg-white md:flex xl:hidden">
        <a href="{{ route('portal.dashboard') }}" class="flex h-20 items-center justify-center gap-1 border-b border-slate-200"><img src="{{ url('/Logo%20Kota%20Bogor.png') }}" class="h-8 w-8 object-contain" alt="Kota Bogor"><img src="{{ asset('logo_ipb.png') }}" class="h-7 w-7 object-contain" alt="IPB"></a>
        <nav class="flex-1 space-y-2 overflow-y-auto p-3">@foreach($navItems as $item)<a href="{{ route($item['route']) }}" title="{{ $item['label'] }}" @class(['flex h-12 items-center justify-center rounded-xl transition','bg-sky-600 text-white shadow-sm'=>$item['active'],'text-slate-500 hover:bg-slate-100 hover:text-slate-950'=>!$item['active']])><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg></a>@endforeach</nav>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" title="Area Admin" aria-label="Area Admin" class="m-3 flex h-12 items-center justify-center rounded-xl text-white shadow-sm" style="background-color: var(--portal-primary-deep);">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 4.5h-6v15h15v-6m-9-3 9-9m0 0h-6m6 0v6"/></svg>
            </a>
        @endif
        <a href="{{ route('beranda') }}" title="Portal Publik" aria-label="Portal Publik" class="mx-3 mb-3 flex h-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3a15 15 0 0 1 0 18m0-18a15 15 0 0 0 0 18M5 5a10 10 0 0 0 14 14M19 5A10 10 0 0 1 5 19"/></svg>
        </a>
    </aside>

    <aside class="fixed inset-y-0 left-0 z-30 hidden w-72 flex-col border-r border-slate-200 bg-white xl:flex">
        <div class="border-b border-slate-200 px-6 py-6">
            <div class="flex items-center gap-3"><div class="flex gap-1"><img src="{{ url('/Logo%20Kota%20Bogor.png') }}" class="h-11 w-11 object-contain" alt="Kota Bogor"><img src="{{ asset('logo_ipb.png') }}" class="h-10 w-10 object-contain" alt="IPB"></div><div><p class="text-[9px] font-bold uppercase tracking-[.22em] text-sky-600">Ruang Kerja ASN</p><p class="mt-1 text-lg font-extrabold text-slate-950">Portal SPBE</p></div></div>
            <p class="mt-3 text-xs leading-relaxed text-slate-500">Tulis, ajukan, dan pantau kontribusi pengetahuan Anda.</p>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" @class(['flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition','bg-sky-600 text-white shadow-sm'=>$item['active'],'text-slate-600 hover:bg-slate-100 hover:text-slate-950'=>!$item['active']])>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $item['active'] ? 'bg-white/15' : 'bg-slate-100 text-slate-500' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg></span>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-slate-200 p-4">
            <p class="truncate px-2 text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p><p class="truncate px-2 text-xs text-slate-500">{{ auth()->user()->unit_kerja ?: auth()->user()->email }}</p>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center justify-between rounded-lg px-3 py-2.5 text-xs font-bold text-white shadow-sm" style="background-color: var(--portal-primary-deep);"><span>Area Admin</span><span aria-hidden="true">→</span></a>
            @endif
            <div class="mt-2 grid grid-cols-2 gap-2"><a href="{{ route('beranda') }}" class="rounded-lg bg-slate-100 px-3 py-2 text-center text-xs font-semibold text-slate-700">Portal Publik</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700">Keluar</button></form></div>
        </div>
    </aside>

    <div class="min-w-0 md:pl-20 xl:pl-72">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur"><div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3"><button type="button" x-on:click="sidebarOpen=true" class="rounded-xl bg-slate-100 p-2.5 md:hidden" aria-label="Buka navigasi"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button><div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900">{{ $title }}</p><p class="hidden text-xs text-slate-500 sm:block">Ruang kontribusi pengetahuan ASN</p></div></div>
            <div class="flex items-center gap-2">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hidden rounded-lg px-3 py-2 text-xs font-bold text-white shadow-sm sm:inline-flex" style="background-color: var(--portal-primary-deep);">Area Admin</a>
                @endif
                <a href="{{ route('beranda') }}" class="hidden rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 sm:inline-flex">Portal Publik</a>
                <a href="{{ route('profile.edit') }}" class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-sky-600 text-xs font-extrabold text-white">@if(auth()->user()->avatar_path)<img src="{{ Storage::disk('public')->url(auth()->user()->avatar_path) }}" class="h-full w-full object-cover" alt="Profil">@else{{ strtoupper(substr(auth()->user()->name,0,1)) }}@endif</a>
            </div>
        </div></header>
        <main class="min-h-[calc(100vh-4rem)] p-3 sm:p-5 lg:p-6 2xl:p-8" style="background:radial-gradient(circle at top right,rgba(14,165,233,.10),transparent 28%),linear-gradient(180deg,#f8fafc,#edf4f8)">
            <div class="mx-auto w-full max-w-[1600px] rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[.24em] text-sky-600">Ruang Kerja ASN</p><h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">{{ $title }}</h1>@if($subtitle)<p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-500">{{ $subtitle }}</p>@endif</div>@isset($actions)<div class="flex flex-wrap gap-3">{{ $actions }}</div>@endisset</div>
                <div class="pt-6">{{ $slot }}</div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
