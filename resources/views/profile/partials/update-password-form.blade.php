<section>
    <header>
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Keamanan</p>
        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Perbarui Password</h2>
        <p class="mt-2 text-sm text-slate-500">Gunakan password panjang dan unik yang tidak digunakan pada layanan lain.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf
        @method('put')

        <div class="sm:col-span-2">
            <label for="update_password_current_password" class="mb-2 block text-sm font-bold text-slate-700">Password Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="mb-2 block text-sm font-bold text-slate-700">Password Baru</label>
            <input id="update_password_password" name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Konfirmasi Password Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-4 sm:col-span-2">
            <button type="submit" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary-deep);">Perbarui Password</button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-700">Password berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
