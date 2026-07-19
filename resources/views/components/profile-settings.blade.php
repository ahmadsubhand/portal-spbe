@props(['user', 'documentTypes'])

<div class="grid gap-6 2xl:grid-cols-[300px_minmax(0,1fr)]">
    <aside class="space-y-5">
        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            @if ($user->avatar_path)
                <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Foto profil {{ $user->name }}" class="h-20 w-20 rounded-2xl object-cover shadow-sm">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl text-3xl font-extrabold text-white shadow-sm" style="background-color: var(--portal-primary-deep);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <h2 class="mt-4 text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
            <p class="mt-1 break-all text-sm text-slate-600">{{ $user->email }}</p>
            @if ($user->jabatan || $user->unit_kerja)
                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ collect([$user->jabatan, $user->unit_kerja])->filter()->join(' · ') }}</p>
            @endif
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-sky-700 shadow-sm">{{ strtoupper($user->role) }}</span>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Informasi Akun</p>
            <dl class="mt-4 space-y-4 text-sm">
                <div>
                    <dt class="text-slate-400">Bergabung</dt>
                    <dd class="mt-1 font-semibold text-slate-700">{{ $user->created_at?->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">Verifikasi email</dt>
                    <dd class="mt-1 font-semibold {{ $user->email_verified_at ? 'text-emerald-700' : 'text-amber-700' }}">{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">Pembaruan terakhir</dt>
                    <dd class="mt-1 font-semibold text-slate-700">{{ $user->updated_at?->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </section>
    </aside>

    <div class="min-w-0 space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <header>
                <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Foto Profil</p>
                <h2 class="mt-2 text-xl font-extrabold text-slate-900">Avatar Akun</h2>
                <p class="mt-2 text-sm text-slate-500">Gunakan foto formal berbentuk persegi. Format JPG, PNG, atau WebP, maksimal 2 MB.</p>
            </header>

            @if ($errors->has('avatar'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first('avatar') }}</div>
            @endif

            @if (in_array(session('status'), ['avatar-updated', 'avatar-deleted'], true))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') === 'avatar-updated' ? 'Foto profil berhasil diperbarui.' : 'Foto profil berhasil dihapus.' }}
                </div>
            @endif

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="flex-1">
                    @csrf
                    @method('PUT')
                    <label for="avatar" class="mb-2 block text-sm font-bold text-slate-700">Pilih foto baru</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                        <button type="submit" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Unggah Foto</button>
                    </div>
                </form>
                @if ($user->avatar_path)
                    <form method="POST" action="{{ route('profile.avatar.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50">Hapus Foto</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @include('profile.partials.update-profile-information-form')
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <header>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Dokumen Profesional</p>
                <h2 class="mt-2 text-xl font-extrabold text-slate-900">Dokumen Pendukung Profil</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Opsional untuk sertifikat kompetensi, pelatihan, atau surat tugas. Jangan unggah KTP, KK, NPWP, data kesehatan, atau dokumen pribadi sensitif. File disimpan privat dan hanya dapat diakses oleh Anda.</p>
            </header>

            @if ($errors->hasAny(['type', 'title', 'document']))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif
            @if (in_array(session('status'), ['document-uploaded', 'document-deleted'], true))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') === 'document-uploaded' ? 'Dokumen berhasil diunggah.' : 'Dokumen berhasil dihapus.' }}</div>
            @endif

            <form method="POST" action="{{ route('profile.documents.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-3 lg:grid-cols-[220px_minmax(0,1fr)_minmax(0,1fr)_auto]">
                @csrf
                <select name="type" class="rounded-xl border-slate-200 bg-slate-50 text-sm" required><option value="">Jenis dokumen</option>@foreach($documentTypes as $value => $label)<option value="{{ $value }}" @selected(old('type')===$value)>{{ $label }}</option>@endforeach</select>
                <input name="title" value="{{ old('title') }}" placeholder="Judul dokumen" class="rounded-xl border-slate-200 bg-slate-50 text-sm" required>
                <input name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm" required>
                <button class="rounded-xl bg-sky-600 px-5 py-3 text-sm font-bold text-white">Unggah</button>
            </form>
            <p class="mt-3 text-xs text-slate-400">Format PDF, JPG, atau PNG. Maksimal 5 MB per dokumen.</p>

            <div class="mt-5 divide-y divide-slate-100 rounded-2xl border border-slate-200">
                @forelse($user->profileDocuments as $document)
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">{{ $document->title }}</p><p class="mt-1 text-xs text-slate-500">{{ $documentTypes[$document->type] ?? $document->type }} · {{ number_format($document->size / 1024, 0) }} KB · {{ $document->created_at->format('d M Y') }}</p></div>
                        <div class="flex gap-2"><a href="{{ route('profile.documents.download', $document) }}" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700">Unduh</a><form method="POST" action="{{ route('profile.documents.destroy', $document) }}" onsubmit="return confirm('Hapus dokumen ini?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form></div>
                    </div>
                @empty
                    <p class="p-6 text-center text-sm text-slate-500">Belum ada dokumen profesional yang diunggah.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @include('profile.partials.update-password-form')
        </section>

        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</div>
