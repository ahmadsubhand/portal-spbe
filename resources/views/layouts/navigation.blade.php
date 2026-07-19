@php
    $publicNavItems = [
        ['label' => 'Beranda', 'route' => 'beranda', 'active' => request()->routeIs('beranda')],
        ['label' => 'Artikel', 'route' => 'artikel.index', 'active' => request()->routeIs('artikel.*')],
        ['label' => 'Forum Diskusi', 'route' => 'forum.index', 'active' => request()->routeIs('forum.*')],
        ['label' => 'Basis Pengetahuan', 'route' => 'knowledge.index', 'active' => request()->routeIs('knowledge.*')],
        ['label' => 'Dashboard', 'route' => 'public-dashboard.index', 'active' => request()->routeIs('public-dashboard.*')],
    ];
@endphp

<nav x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" class="sticky top-0 z-30 border-b border-blue-400/20 shadow-sm" style="background-color: var(--portal-secondary);" aria-label="Navigasi utama">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-20 items-center justify-between gap-4">
            <a href="{{ route('beranda') }}" class="flex-shrink-0 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80">
                <x-portal-brand dark size="sm" />
            </a>

            <div class="hidden items-stretch self-stretch md:flex">
                @foreach ($publicNavItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        @class([
                            'relative flex items-center px-3 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/80 lg:px-4',
                            'text-white' => $item['active'],
                            'text-white/80 hover:text-white' => ! $item['active'],
                        ])
                        @if ($item['active']) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                        @if ($item['active'])
                            <span class="absolute inset-x-3 bottom-0 h-1 rounded-t-full bg-white lg:inset-x-4" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'portal.dashboard') }}" class="rounded-lg bg-white/10 px-3 py-2 text-xs font-bold text-white transition hover:bg-white/20">
                        {{ auth()->user()->isAdmin() ? 'Area Admin' : 'Area ASN' }}
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-white/30 bg-white/10 text-xs font-extrabold text-white transition hover:bg-white/20" aria-label="Buka menu akun {{ Auth::user()->name }}">
                                @if (Auth::user()->avatar_path)
                                    <img src="{{ Storage::disk('public')->url(Auth::user()->avatar_path) }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            @if (auth()->user()->isAdmin())
                                <x-dropdown-link :href="route('portal.dashboard')">Area ASN</x-dropdown-link>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">Keluar</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:opacity-90" style="background-color: var(--portal-primary-deep);">Masuk</a>
                @endauth
            </div>

            <button type="button" x-on:click="menuOpen = !menuOpen" class="rounded-xl bg-white/10 p-2.5 text-white md:hidden" :aria-expanded="menuOpen" aria-controls="public-mobile-menu" aria-label="Buka navigasi">
                <svg x-show="!menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-cloak x-show="menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <div x-cloak x-show="menuOpen" x-transition id="public-mobile-menu" class="border-t border-white/15 pb-4 pt-3 md:hidden">
            <div class="space-y-1">
                @foreach ($publicNavItems as $item)
                    <a href="{{ route($item['route']) }}" @class(['flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition', 'bg-white text-blue-800' => $item['active'], 'text-white/85 hover:bg-white/10 hover:text-white' => ! $item['active']]) @if ($item['active']) aria-current="page" @endif>
                        <span>{{ $item['label'] }}</span>
                        @if ($item['active'])<span class="h-2 w-2 rounded-full bg-blue-600" aria-hidden="true"></span>@endif
                    </a>
                @endforeach
            </div>
            <div class="mt-3 border-t border-white/15 pt-3">
                @auth
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'portal.dashboard') }}" class="rounded-xl bg-white px-4 py-3 text-center text-sm font-bold text-blue-800">{{ auth()->user()->isAdmin() ? 'Area Admin' : 'Area ASN' }}</a>
                        <a href="{{ route('profile.edit') }}" class="rounded-xl bg-white/10 px-4 py-3 text-center text-sm font-bold text-white">Profil</a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block rounded-xl bg-white px-4 py-3 text-center text-sm font-bold text-blue-800">Masuk ke Portal Internal</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
