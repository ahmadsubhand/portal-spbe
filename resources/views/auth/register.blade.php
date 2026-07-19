<x-guest-layout>
    <div class="min-h-screen bg-white font-sans antialiased md:grid md:grid-cols-[minmax(0,1fr)_minmax(360px,0.72fr)]">
        <main class="relative flex min-h-screen items-center px-6 py-24 sm:px-12 lg:px-20" style="background-color: var(--portal-bg);">
            <a href="{{ route('beranda') }}" class="absolute left-6 top-7 inline-flex items-center gap-2 text-xs font-semibold text-blue-700 no-underline hover:underline sm:left-12">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="mx-auto w-full max-w-2xl">
                <x-portal-brand size="lg" />

                <p class="mt-10 text-xs font-bold uppercase tracking-[0.24em]" style="color: var(--portal-primary);">Akses Internal Resmi</p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight text-slate-950 sm:text-4xl">Permintaan Akses Portal</h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600">
                    Akun ASN dan administrator diterbitkan oleh administrator portal setelah identitas, unit kerja, dan kebutuhan akses diverifikasi.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach ([
                        ['1', 'Hubungi Admin', 'Sampaikan kebutuhan akses melalui administrator SPBE unit Anda.'],
                        ['2', 'Verifikasi Data', 'Administrator memeriksa identitas dan kewenangan akun.'],
                        ['3', 'Terima Akun', 'Kredensial dan peran diberikan sesuai kebutuhan kerja.'],
                    ] as [$number, $title, $description])
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl text-sm font-extrabold text-white" style="background-color: var(--portal-primary);">{{ $number }}</span>
                            <h2 class="mt-4 text-sm font-bold text-slate-900">{{ $title }}</h2>
                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $description }}</p>
                        </section>
                    @endforeach
                </div>

                <div class="mt-8 rounded-2xl border border-blue-200 bg-blue-50/80 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-blue-700 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2h-1v2m0 4h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Sudah memiliki akun?</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-600">Gunakan email dan password yang diberikan administrator. Jangan membagikan kredensial kepada pihak lain.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-xs font-bold text-white no-underline shadow-sm transition hover:opacity-95" style="background-color: var(--portal-primary);">
                        Kembali ke Login
                    </a>
                    <a href="{{ route('beranda') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs font-bold text-slate-700 no-underline transition hover:bg-slate-50">
                        Lihat Portal Publik
                    </a>
                </div>
                <p class="mt-5 text-xs leading-5 text-slate-500">
                    Bantuan akses:
                    <a href="mailto:{{ $portalSettings[\App\Models\Setting::PUBLIC_CONTACT_EMAIL] ?? 'kontak@bogorkota.go.id' }}" class="font-bold text-blue-700 no-underline hover:underline">
                        {{ $portalSettings[\App\Models\Setting::PUBLIC_CONTACT_EMAIL] ?? 'kontak@bogorkota.go.id' }}
                    </a>
                </p>
            </div>
        </main>

        <aside class="relative hidden overflow-hidden px-12 py-16 text-white md:flex md:flex-col md:justify-between" style="background-color: var(--portal-secondary);">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-slate-950/20"></div>
            <div class="absolute -right-28 -top-28 h-72 w-72 rounded-full border border-white/10"></div>
            <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full border border-white/10"></div>

            <div class="relative z-10">
                <x-portal-brand dark size="lg" />
                <p class="mt-12 text-xs font-bold uppercase tracking-[0.24em] text-white/60">Kebijakan Akses</p>
                <h2 class="mt-4 max-w-md text-3xl font-extrabold leading-tight">Akun internal dikelola untuk menjaga akuntabilitas layanan.</h2>
                <p class="mt-5 max-w-md text-sm leading-7 text-white/75">
                    Portal tidak membuka pendaftaran mandiri dan tidak menggunakan akun media sosial pribadi untuk akses internal.
                </p>
            </div>

            <div class="relative z-10 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/60">Butuh Bantuan?</p>
                <p class="mt-3 text-sm leading-6 text-white/85">Hubungi administrator SPBE atau pengelola akun pada unit kerja Anda melalui kanal resmi portal.</p>
            </div>
        </aside>
    </div>
</x-guest-layout>
