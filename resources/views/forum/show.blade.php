<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-7xl px-6 py-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-3 text-sm text-slate-500">
                <a href="{{ route('beranda') }}" class="hover:text-slate-700 transition">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ route('forum.index') }}" class="hover:text-slate-700 transition">Forum Diskusi</a>
                <span>&rsaquo;</span>
                <span class="max-w-[50vw] truncate font-semibold text-slate-800">{{ $topic->title }}</span>
            </nav>
            <a href="{{ route('forum.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19 3 12l7-7m-7 7h18"/></svg>
                Kembali ke Forum
            </a>

            @can('toggleActive', $topic)
                <div class="mt-5 flex flex-wrap items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="mr-auto text-sm font-semibold text-amber-800">{{ $topic->isActive() ? 'Topik ini aktif dan terlihat di portal.' : 'Topik ini nonaktif dan hanya dapat dilihat pemilik serta admin.' }}</p>
                    <form method="POST" action="{{ route('forum.toggle-active', $topic) }}" onsubmit="return confirm('{{ $topic->isActive() ? 'Nonaktifkan topik ini dari portal?' : 'Aktifkan kembali topik ini di portal?' }}')">@csrf @method('PATCH')<button class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white">{{ $topic->isActive() ? 'Nonaktifkan' : 'Aktifkan kembali' }}</button></form>
                    <form method="POST" action="{{ route('forum.destroy', $topic->id) }}" onsubmit="return confirm('Hapus topik ini secara permanen?')">@csrf @method('DELETE')<button class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white">Hapus permanen</button></form>
                </div>
            @endcan

            <header class="mt-8 rounded-[28px] border border-slate-200 bg-gradient-to-br from-sky-50 via-white to-white p-6 shadow-sm sm:p-8">
                <div class="max-w-4xl">
                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $topic->category }}</span>
                    <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-950">
                        {{ $topic->title }}
                    </h1>
                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-slate-500">
                        <span>Oleh <strong class="text-slate-700">{{ $topic->user->name ?? 'Anonim' }}</strong></span>
                        <span class="inline-flex items-center gap-1.5"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M5 6h14v10H9l-4 4V6Z"/></svg>{{ $topic->replies->count() }} balasan</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"/><circle cx="12" cy="12" r="2"/></svg>{{ $topic->views_count }} dilihat</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M7 10v10H4V10h3Zm3 10h7a2 2 0 0 0 2-1.5l1.5-6A2 2 0 0 0 18.5 10H15l1-4a2 2 0 0 0-2-2l-4 6v10Z"/></svg>{{ $topic->likes->count() }} suka</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M8 3v3m8-3v3M4 10h16M5 5h14v15H5V5Z"/></svg>{{ $topic->created_at->translatedFormat('d M Y') }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $topic->isOpen() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-800 text-white' }}">{{ $topic->isOpen() ? 'Diskusi terbuka' : 'Diskusi tertutup' }}</span>
                    </div>
                </div>
            </header>

            <div class="mt-10 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4 border-l-4 pl-5" style="border-color: var(--portal-primary);">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold uppercase" style="color: var(--portal-primary);">
                        {{ strtoupper(\Illuminate\Support\Str::of($topic->user->name ?? 'AN')->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('')) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-slate-900">{{ $topic->user->name ?? 'Anonim' }}</h2>
                            <span class="text-sm text-slate-500">{{ $topic->created_at->diffForHumans() }}</span>
                        </div>

                        @auth
                            <div class="prose prose-slate mt-5 max-w-none text-lg leading-9 text-slate-700">
                                {!! $topic->content !!}
                            </div>
                        @else
                            {{-- Guest: tampilkan preview ringkasan saja --}}
                            <p class="mt-5 text-lg leading-9 text-slate-600">{{ $preview }}</p>
                        @endauth

                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-700">
                                #{{ str_replace(' ', '-', \Illuminate\Support\Str::lower($topic->category)) }}
                            </span>
                            @foreach (collect(preg_split('/\s+/', $topic->title))->map(fn ($word) => trim($word, ".,:;!?\"'"))->filter(fn ($word) => strlen($word) >= 5)->take(2) as $keyword)
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-700">
                                    #{{ $keyword }}
                                </span>
                            @endforeach
                        </div>

                        @auth
                            <div id="topic-like" class="mt-8">
                                <form action="{{ route('forum.like.toggle', $topic->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="return_to" value="{{ request()->fullUrl().'#topic-like' }}">
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $topic->likes->where('user_id', auth()->id())->first() ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">
                                        <span>Suka</span>
                                        <span>{{ $topic->likes->count() }}</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Guest: CTA masuk untuk berinteraksi --}}
                            <div class="mt-10 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-6 text-center">
                                <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11V7a4 4 0 1 1 8 0v4m-8 0H6a2 2 0 0 0-2 2v6h16v-6a2 2 0 0 0-2-2h-6Z"/></svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-900">Masuk untuk membaca & ikut berdiskusi</h3>
                                <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500">Balasan dan interaksi hanya tersedia untuk akun internal yang telah diverifikasi.</p>
                                <div class="mt-5 flex flex-col justify-center gap-3 sm:flex-row">
                                    <a href="{{ route('login') }}" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Masuk</a>
                                    <a href="{{ route('register') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700">Minta akses</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            @auth
                <div class="mt-8 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900">{{ $topic->replies->sum(fn ($reply) => 1 + $reply->children->count()) }} Balasan</h3>

                    <div class="mt-6 divide-y divide-slate-200">
                        @forelse ($topic->replies as $reply)
                            <article id="reply-{{ $reply->id }}" x-data="{ replying: false }" class="flex items-start gap-4 py-5">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold uppercase text-slate-700">
                                    {{ strtoupper(\Illuminate\Support\Str::of($reply->user->name ?? 'AN')->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('')) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="text-sm font-bold text-slate-900">{{ $reply->user->name ?? 'Anonim' }}</span>
                                        <span class="text-sm text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $reply->content }}</p>
                                    @auth
                                        <div class="mt-3 flex items-center gap-4 text-xs font-bold text-slate-500">
                                            <form action="{{ route('forum.reply.like', $reply) }}" method="POST">@csrf<input type="hidden" name="return_to" value="{{ request()->fullUrl().'#reply-'.$reply->id }}"><button class="inline-flex items-center gap-1.5 hover:text-blue-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M7 10v10H4V10h3Zm3 10h7a2 2 0 0 0 2-1.5l1.5-6A2 2 0 0 0 18.5 10H15l1-4a2 2 0 0 0-2-2l-4 6v10Z"/></svg>{{ $reply->likes->count() }} suka</button></form>
                                            <button type="button" x-on:click="replying = !replying" class="hover:text-blue-700">Balas</button>
                                        </div>
                                        <form x-cloak x-show="replying" action="{{ route('forum.reply.store', $topic->id) }}" method="POST" class="mt-4 flex gap-2">@csrf<input type="hidden" name="parent_id" value="{{ $reply->id }}"><input name="content" required minlength="2" placeholder="Balas komentar ini..." class="min-w-0 flex-1 rounded-xl border-slate-200 bg-slate-50 text-sm"><button class="rounded-xl bg-blue-600 px-4 text-xs font-bold text-white">Kirim</button></form>
                                    @endauth
                                    @if ($reply->children->isNotEmpty())
                                        <div class="mt-5 space-y-4 border-l-2 border-slate-200 pl-4 sm:pl-6">
                                            @foreach ($reply->children as $child)
                                                <div id="reply-{{ $child->id }}" class="rounded-xl bg-slate-50 p-4">
                                                    <div class="flex flex-wrap gap-2 text-xs"><strong class="text-slate-800">{{ $child->user?->name ?? 'Anonim' }}</strong><span class="text-slate-400">{{ $child->created_at->diffForHumans() }}</span></div>
                                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $child->content }}</p>
                                                    @auth<form action="{{ route('forum.reply.like', $child) }}" method="POST" class="mt-3">@csrf<input type="hidden" name="return_to" value="{{ request()->fullUrl().'#reply-'.$child->id }}"><button class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-700">Suka · {{ $child->likes->count() }}</button></form>@endauth
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="py-8 text-center text-sm text-slate-500">
                                Belum ada balasan untuk topik ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                @auth<div class="mt-8 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900">Tulis Balasan</h3>
                    <form action="{{ route('forum.reply.store', $topic->id) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <textarea name="content" rows="5" required placeholder="Bagikan tanggapan Anda..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 focus:border-slate-300 focus:bg-white focus:outline-none"></textarea>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm text-slate-400">Gunakan bahasa yang sopan dan konstruktif.</p>
                            <button type="submit" class="rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-sm" style="background-color: var(--portal-primary);">
                                Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>@endauth
            @endauth
        </div>
    </div>
</x-app-layout>
