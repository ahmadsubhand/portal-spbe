<x-guest-layout>
    <div class="min-h-screen flex flex-col md:flex-row font-sans antialiased bg-white">
        
        <div class="w-full md:w-[45%] flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12 relative" style="background-color: var(--portal-bg);">
            
            <a href="{{ route('beranda') }}" class="absolute top-8 left-8 inline-flex items-center gap-2 text-xs font-semibold text-blue-700 no-underline hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="w-full max-w-md mx-auto text-center mt-8">
                <div class="mb-4 flex justify-center">
                    <x-portal-brand centered size="xl" />
                </div>

                <h1 class="text-2xl font-extrabold mt-6 mb-1 text-gray-800">Selamat Datang</h1>
                <p class="text-xs text-gray-500 m-0">Masuk menggunakan akun internal yang diterbitkan instansi</p>

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 mt-4">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 text-xs text-red-600 text-left mt-4 bg-red-50 p-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="m-0">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-8 text-left space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                        <div class="relative flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden focus-within:border-blue-500 transition shadow-sm">
                            <span class="pl-3 text-gray-400 flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L22 8m-2 5v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4"/></svg>
                            </span>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email Anda" class="w-full border-0 focus:ring-0 text-xs py-3 px-3 text-gray-700 placeholder-gray-400 outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                        <div class="relative flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden focus-within:border-blue-500 transition shadow-sm">
                            <span class="pl-3 text-gray-400 flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda" class="w-full border-0 focus:ring-0 text-xs py-3 px-3 text-gray-700 placeholder-gray-400 outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-3.5 h-3.5">
                            <span class="ms-2 text-[11px] font-medium text-gray-500">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-blue-500 no-underline hover:underline">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-lg text-xs tracking-wide shadow-md transition hover:opacity-95 text-center block border-0 cursor-pointer" style="background-color: var(--portal-primary);">
                        Masuk
                    </button>

                    <div class="rounded-xl border border-blue-200 bg-blue-50/80 p-3.5 text-left">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4m-8 0H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-4zm0 0v4"/>
                            </svg>
                            <div>
                                <p class="m-0 text-[11px] font-bold text-slate-700">Akses internal resmi</p>
                                <p class="mt-1 mb-0 text-[10px] leading-4 text-slate-500">Gunakan akun yang diberikan administrator dan jangan membagikan kredensial Anda.</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-center text-[11px] text-gray-500 pt-2 font-medium m-0">
                        Belum memiliki akses? <a href="{{ route('register') }}" class="text-blue-600 font-bold no-underline hover:underline">Lihat proses permintaan akses</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-full md:w-[55%] flex-col justify-center items-center px-16 text-white text-center relative" style="background-color: var(--portal-secondary);">
            
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-black/10 pointer-events-none"></div>

            <div class="w-full max-w-lg z-10">
                <div class="mx-auto mb-6 flex justify-center">
                    <x-portal-brand dark centered size="xl" :show-text="false" />
                </div>

                <h2 class="text-3xl font-extrabold tracking-tight mb-3">Sistem Manajemen<br>Pengetahuan</h2>
                <p class="text-sm text-white/80 max-w-md mx-auto mb-10 leading-relaxed">
                    Platform terpadu untuk mengelola dan berbagi pengetahuan di lingkungan Pemerintah Kota Bogor
                </p>

                <div class="space-y-4 text-left w-full max-w-md mx-auto flex flex-col gap-3">
                    <div class="bg-white/10 border border-white/10 rounded-xl p-4 flex items-center gap-4 backdrop-blur-sm shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/15 bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V2H6.5A2.5 2.5 0 004 4.5v15z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight m-0">Basis Pengetahuan Terpusat</h4>
                            <p class="text-xs text-white/70 mt-0.5 mb-0">Akses informasi dan dokumen penting dalam satu platform.</p>
                        </div>
                    </div>

                    <div class="bg-white/10 border border-white/10 rounded-xl p-4 flex items-center gap-4 backdrop-blur-sm shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/15 bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2h-6l-4 2v-4H5a2 2 0 01-2-2V8a2 2 0 012-2h2m2-3h6a2 2 0 012 2v6a2 2 0 01-2 2h-2l-4 2v-4H9a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight m-0">Kolaborasi & Diskusi</h4>
                            <p class="text-xs text-white/70 mt-0.5 mb-0">Berbagi ide dan solusi dengan rekan kerja dengan mudah.</p>
                        </div>
                    </div>

                    <div class="bg-white/10 border border-white/10 rounded-xl p-4 flex items-center gap-4 backdrop-blur-sm shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/15 bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19h16M5 16l4-4 3 3 6-7M5 16V5m14 11V5"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight m-0">Dashboard Terintegrasi</h4>
                            <p class="text-xs text-white/70 mt-0.5 mb-0">Pantau aktivitas dan analitik data secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>
