<section>
    <header>
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Status Akses</p>
        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Ajukan Penonaktifan Akun</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">Admin akan meninjau kebutuhan retensi data sebelum menonaktifkan akun. Artikel, diskusi, dan audit aktivitas tidak akan dihapus.</p>
    </header>

    @if ($errors->has('deactivation'))
        <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $errors->first('deactivation') }}</p>
    @endif

    @if ($user->pendingDeactivationRequest)
        <div class="mt-5 rounded-2xl border border-amber-200 bg-white p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Menunggu review admin</span>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $user->pendingDeactivationRequest->reason }}</p>
                    <p class="mt-2 text-xs text-slate-400">Diajukan {{ $user->pendingDeactivationRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <form method="POST" action="{{ route('profile.deactivation-request.cancel') }}">
                    @csrf @method('DELETE')
                    <button class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Batalkan pengajuan</button>
                </form>
            </div>
        </div>
    @else
        <button x-data x-on:click.prevent="$dispatch('open-modal', 'request-account-deactivation')" class="mt-5 rounded-xl bg-amber-600 px-5 py-3 text-sm font-bold text-white">Ajukan Penonaktifan</button>

        <x-modal name="request-account-deactivation" :show="$errors->hasAny(['reason', 'password'])" focusable maxWidth="lg">
            <form method="POST" action="{{ route('profile.deactivation-request.store') }}" class="p-6">
                @csrf
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Pengajuan Penonaktifan</p>
                <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Mengapa akun perlu dinonaktifkan?</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Akun tetap aktif sampai admin menyetujui pengajuan. Seluruh konten dan rekam audit akan dipertahankan.</p>
                <div class="mt-6">
                    <label for="deactivation_reason" class="mb-2 block text-sm font-bold text-slate-700">Alasan pengajuan</label>
                    <textarea id="deactivation_reason" name="reason" rows="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Jelaskan alasan dan konteks penonaktifan akun..." required>{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <label for="deactivation_password" class="mb-2 block text-sm font-bold text-slate-700">Konfirmasi password</label>
                    <input id="deactivation_password" name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Batal</button>
                    <button type="submit" class="rounded-xl bg-amber-600 px-4 py-3 text-sm font-bold text-white">Kirim pengajuan</button>
                </div>
            </form>
        </x-modal>
    @endif
</section>
