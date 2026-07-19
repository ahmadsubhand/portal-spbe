<x-admin-layout title="Audit Log" subtitle="Pantau perubahan administratif dan aktivitas workflow untuk menjaga jejak operasional portal.">
    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-600">Total Aktivitas</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Seluruh aktivitas yang tercatat.</p>
        </section>
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            <p class="text-sm font-semibold text-sky-800">Aktivitas Hari Ini</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['today'] }}</p>
            <p class="mt-2 text-xs text-sky-700">Aktivitas sejak awal hari ini.</p>
        </section>
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-sm font-semibold text-emerald-800">Aktor Tercatat</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['actors'] }}</p>
            <p class="mt-2 text-xs text-emerald-700">Pengguna yang pernah melakukan aksi.</p>
        </section>
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-sm font-semibold text-amber-800">Jenis Aksi</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['actions'] }}</p>
            <p class="mt-2 text-xs text-amber-700">Variasi event yang sedang dicatat.</p>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Riwayat Aktivitas</h3>
                    <p class="mt-1 text-sm text-slate-500">Filter berdasarkan jenis aksi atau nama aktor untuk investigasi yang lebih cepat.</p>
                </div>
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid w-full gap-3 sm:grid-cols-[220px_minmax(0,1fr)_auto] lg:max-w-3xl">
                    <select name="action" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <option value="">Semua aksi</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ str($action)->replace('.', ' ')->title() }}</option>
                        @endforeach
                    </select>
                    <input name="actor" type="search" value="{{ $filters['actor'] }}" placeholder="Cari nama atau email aktor" class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Terapkan</button>
                </form>
            </div>
        </div>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse ($logs as $log)
                @php
                    $actionTone = str_contains($log->action, 'rejected') || str_contains($log->action, 'deleted')
                        ? 'bg-rose-100 text-rose-700'
                        : (str_contains($log->action, 'published') || str_contains($log->action, 'approved') || str_contains($log->action, 'created')
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-sky-100 text-sky-700');
                @endphp
                <article class="p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $actionTone }}">{{ str($log->action)->replace('.', ' ')->title() }}</span>
                        <time class="text-xs text-slate-400">{{ $log->created_at?->format('d M Y H:i') }}</time>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-900">{{ $log->user?->name ?? 'Pengguna tidak tersedia' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ class_basename($log->subject_type) }} #{{ $log->subject_id ?? '-' }}</p>
                    @if ($log->metadata)
                        <details class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500">
                            <summary class="cursor-pointer font-semibold text-slate-700">Lihat metadata</summary>
                            <pre class="mt-2 overflow-x-auto whitespace-pre-wrap">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </details>
                    @endif
                </article>
            @empty
                <div class="p-10 text-center text-sm text-slate-500">Belum ada aktivitas yang cocok dengan filter.</div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Waktu</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Aktor</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Aksi</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Subjek</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        @php
                            $actionTone = str_contains($log->action, 'rejected') || str_contains($log->action, 'deleted')
                                ? 'bg-rose-100 text-rose-700'
                                : (str_contains($log->action, 'published') || str_contains($log->action, 'approved') || str_contains($log->action, 'created')
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-sky-100 text-sky-700');
                        @endphp
                        <tr class="align-top transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ $log->user?->name ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $log->user?->email ?? 'Akun tidak tersedia' }}</p>
                            </td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $actionTone }}">{{ str($log->action)->replace('.', ' ')->title() }}</span></td>
                            <td class="px-5 py-4 text-slate-600">{{ class_basename($log->subject_type) }} <span class="text-slate-400">#{{ $log->subject_id ?? '-' }}</span></td>
                            <td class="max-w-sm px-5 py-4">
                                @if ($log->metadata)
                                    <details class="text-xs text-slate-500">
                                        <summary class="cursor-pointer font-semibold text-slate-700">Lihat metadata</summary>
                                        <pre class="mt-2 max-w-xs overflow-x-auto whitespace-pre-wrap rounded-lg bg-slate-100 p-3">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @else
                                    <span class="text-xs text-slate-400">Tidak ada metadata</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada aktivitas yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                {{ $logs->onEachSide(1)->links() }}
            </div>
        @endif
    </section>
</x-admin-layout>
