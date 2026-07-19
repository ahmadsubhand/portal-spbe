<x-admin-layout title="Manajemen Pengguna" subtitle="Kelola akun admin, ASN, dan status akses umum lewat daftar ringkas yang aman untuk skala data lebih besar.">
    <x-slot name="actions">
        <button
            type="button"
            x-data
            x-on:click="$dispatch('open-modal', 'create-user-modal')"
            class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm"
            style="background-color: var(--portal-primary);"
        >
            Tambah Pengguna
        </button>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if ($errors->has('user'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ $errors->first('user') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-600">Total Pengguna</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Semua akun yang terdaftar di portal.</p>
        </section>
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-sm font-semibold text-emerald-800">Akun Aktif</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['active'] }}</p>
            <p class="mt-2 text-sm text-emerald-700">Pengguna yang saat ini diizinkan login.</p>
        </section>
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            <p class="text-sm font-semibold text-sky-800">Admin</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['admin'] }}</p>
            <p class="mt-2 text-sm text-sky-700">Akun dengan akses review dan konfigurasi.</p>
        </section>
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-sm font-semibold text-amber-800">ASN</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stats['asn'] }}</p>
            <p class="mt-2 text-sm text-amber-700">Kontributor internal untuk draft dan submit artikel.</p>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
        <div class="border-b border-amber-200 bg-amber-50 p-5 sm:p-6">
            <h3 class="text-lg font-bold text-slate-900">Pengajuan Penonaktifan</h3>
            <p class="mt-1 text-sm text-amber-800">Tinjau alasan pengguna sebelum menonaktifkan akses. Konten pengguna tetap dipertahankan.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($deactivationRequests as $deactivationRequest)
                <article class="p-5 sm:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><h4 class="font-bold text-slate-900">{{ $deactivationRequest->user->name }}</h4><span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Menunggu keputusan</span></div>
                            <p class="mt-1 text-sm text-slate-500">{{ $deactivationRequest->user->email }} · {{ strtoupper($deactivationRequest->user->role) }}</p>
                            <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-600">{{ $deactivationRequest->reason }}</p>
                            <p class="mt-2 text-xs text-slate-400">Diajukan {{ $deactivationRequest->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'reject-deactivation-{{ $deactivationRequest->id }}')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700">Tolak</button>
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'approve-deactivation-{{ $deactivationRequest->id }}')" class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white">Setujui & nonaktifkan</button>
                        </div>
                    </div>
                </article>
                @foreach (['approve' => ['Setujui Penonaktifan', 'Akun akan dinonaktifkan. Artikel, diskusi, dan audit aktivitas tetap dipertahankan.', 'Setujui & nonaktifkan', 'bg-amber-600'], 'reject' => ['Tolak Pengajuan', 'Pengguna tetap dapat mengakses portal. Tambahkan catatan jika perlu menjelaskan keputusan.', 'Tolak pengajuan', 'bg-slate-800']] as $action => $copy)
                    <x-modal name="{{ $action }}-deactivation-{{ $deactivationRequest->id }}" focusable maxWidth="lg">
                        <form method="POST" action="{{ route('admin.deactivation-requests.'.$action, $deactivationRequest) }}" class="p-6">
                            @csrf
                            <h3 class="text-2xl font-extrabold text-slate-950">{{ $copy[0] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ $copy[1] }}</p>
                            <textarea name="admin_note" rows="4" placeholder="Catatan admin (opsional)" class="mt-5 w-full rounded-xl border-slate-200 bg-slate-50 text-sm"></textarea>
                            <div class="mt-6 flex justify-end gap-3"><button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button><button type="submit" class="rounded-xl {{ $copy[3] }} px-4 py-3 text-sm font-bold text-white">{{ $copy[2] }}</button></div>
                        </form>
                    </x-modal>
                @endforeach
            @empty
                <div class="p-10 text-center text-sm text-slate-500">Tidak ada pengajuan penonaktifan yang menunggu.</div>
            @endforelse
        </div>
        @if($deactivationRequests->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $deactivationRequests->links() }}</div>@endif
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-slate-900">Daftar Pengguna</h3>
                <p class="mt-1 text-sm text-slate-500">Cari akun, tinjau role, dan kelola status akses pengguna portal.</p>
            </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto]">
            <input name="q" type="text" value="{{ $filters['q'] }}" placeholder="Cari nama atau email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
            <select name="role" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                <option value="">Semua role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected($filters['role'] === $role)>{{ strtoupper($role) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Filter</button>
        </form>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($users as $managedUser)
                <article class="p-5 transition hover:bg-slate-50/60 sm:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-slate-900">{{ $managedUser->name }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $managedUser->role === 'admin' ? 'bg-sky-100 text-sky-800' : ($managedUser->role === 'asn' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ strtoupper($managedUser->role) }}
                                </span>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $managedUser->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $managedUser->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <p class="mt-2 break-all text-sm text-slate-500">{{ $managedUser->email }}</p>
                            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-400">
                                <span>ID #{{ $managedUser->id }}</span>
                                <span>Dibuat {{ $managedUser->created_at?->format('d M Y H:i') }}</span>
                                <span>Update {{ $managedUser->updated_at?->format('d M Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('open-modal', 'edit-user-{{ $managedUser->id }}')"
                                class="rounded-xl px-4 py-2 text-sm font-bold text-white"
                                style="background-color: var(--portal-primary);"
                            >
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.users.toggle-active', $managedUser) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                    {{ $managedUser->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @can('delete', $managedUser)
                                <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Hapus permanen akun kosong ini? Tindakan tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Hapus akun kosong</button>
                                </form>
                            @else
                                @if (! auth()->user()->is($managedUser) && ($managedUser->artikels_count > 0 || $managedUser->topics_count > 0))
                                    <span class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500" title="Akun memiliki konten dan hanya dapat dinonaktifkan.">Data dipertahankan</span>
                                @endif
                            @endcan
                        </div>
                    </div>
                </article>

                <x-modal name="edit-user-{{ $managedUser->id }}" focusable maxWidth="2xl">
                    <div class="p-6 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Edit Pengguna</p>
                                <h3 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $managedUser->name }}</h3>
                                <p class="mt-2 text-sm text-slate-500">Perbarui data akun tanpa memenuhi halaman utama dengan form edit langsung.</p>
                            </div>
                            <button type="button" x-on:click="$dispatch('close-modal', 'edit-user-{{ $managedUser->id }}')" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-500">Tutup</button>
                        </div>

                        <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-700">Nama</label>
                                <input name="name" type="text" value="{{ $managedUser->name }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                                <input name="email" type="email" value="{{ $managedUser->email }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">Role</label>
                                <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ strtoupper($role) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">Status</label>
                                <label class="flex min-h-[48px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" @checked($managedUser->is_active)>
                                    Akun aktif
                                </label>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">Password Baru</label>
                                <input name="password" type="password" placeholder="Opsional" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">Konfirmasi Password</label>
                                <input name="password_confirmation" type="password" placeholder="Ulangi password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </div>
                            <div class="flex flex-wrap justify-end gap-3 sm:col-span-2">
                                <button type="button" x-on:click="$dispatch('close-modal', 'edit-user-{{ $managedUser->id }}')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                                <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @empty
                <div class="p-12 text-center">
                    <h4 class="font-bold text-slate-900">Tidak ada pengguna ditemukan</h4>
                    <p class="mt-2 text-sm text-slate-500">Ubah kata kunci atau filter role untuk melihat akun lainnya.</p>
                </div>
            @endforelse
        </div>

        @if ($users->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p>
                    Menampilkan
                    <span class="font-semibold text-slate-700">{{ $users->firstItem() }}</span>
                    -
                    <span class="font-semibold text-slate-700">{{ $users->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-slate-700">{{ $users->total() }}</span>
                    pengguna
                </p>
                <div>
                    {{ $users->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </section>

    <x-modal name="create-user-modal" focusable maxWidth="2xl">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Tambah Pengguna</p>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Akun Baru</h3>
                    <p class="mt-2 text-sm text-slate-500">Buat akun admin, ASN, atau guest tanpa mengambil ruang permanen di halaman manajemen.</p>
                </div>
                <button type="button" x-on:click="$dispatch('close-modal', 'create-user-modal')" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-500">Tutup</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Nama</label>
                    <input name="name" type="text" placeholder="Nama pengguna" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Email</label>
                    <input name="email" type="email" placeholder="email@spbe.test" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Role</label>
                    <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}">{{ strtoupper($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Status</label>
                    <label class="flex min-h-[48px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Akun aktif
                    </label>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                    <input name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Konfirmasi Password</label>
                    <input name="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                </div>
                <div class="flex flex-wrap justify-end gap-3 sm:col-span-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'create-user-modal')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-xl px-4 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-admin-layout>
