<x-app-layout>
    <div class="min-h-screen" style="background-color: var(--portal-bg);">
        <div class="max-w-6xl mx-auto px-6 py-14">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: var(--portal-primary);">Informasi SPBE</p>
                <h1 class="mt-3 text-3xl font-extrabold" style="color: var(--portal-primary-deep);">Dashboard Publik</h1>
                <p class="mt-4 max-w-3xl text-sm leading-relaxed text-slate-600">
                    Lihat ringkasan indikator dan informasi utama untuk membantu memahami perkembangan layanan SPBE.
                </p>

                @php($embedUrl = $portalSettings[\App\Models\Setting::DASHBOARD_EMBED_URL] ?? null)

                @if ($embedUrl)
                    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
                        <iframe src="{{ $embedUrl }}" class="h-[720px] w-full bg-slate-50" loading="lazy"></iframe>
                    </div>
                @else
                    <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500">
                        Dashboard belum tersedia untuk ditampilkan.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
