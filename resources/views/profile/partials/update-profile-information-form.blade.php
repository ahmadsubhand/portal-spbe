<section>
    <header>
        <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--portal-primary);">Identitas</p>
        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Informasi Profil</h2>
        <p class="mt-2 text-sm text-slate-500">Perbarui identitas dan informasi kerja yang digunakan untuk kolaborasi pada portal ini.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama</label>
            <input id="name" name="name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email</label>
            <input id="email" name="email" type="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <label for="nip" class="mb-2 block text-sm font-bold text-slate-700">NIP</label>
            <input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('nip', $user->nip) }}" placeholder="18 digit NIP">
            <x-input-error class="mt-2" :messages="$errors->get('nip')" />
        </div>

        <div>
            <label for="phone_number" class="mb-2 block text-sm font-bold text-slate-700">Nomor Kontak</label>
            <input id="phone_number" name="phone_number" type="tel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+62...">
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <label for="unit_kerja" class="mb-2 block text-sm font-bold text-slate-700">Unit Kerja</label>
            <input id="unit_kerja" name="unit_kerja" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('unit_kerja', $user->unit_kerja) }}" placeholder="Contoh: Diskominfo Kota Bogor">
            <x-input-error class="mt-2" :messages="$errors->get('unit_kerja')" />
        </div>

        <div>
            <label for="jabatan" class="mb-2 block text-sm font-bold text-slate-700">Jabatan</label>
            <input id="jabatan" name="jabatan" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Pranata Komputer Ahli Muda">
            <x-input-error class="mt-2" :messages="$errors->get('jabatan')" />
        </div>

        <div class="sm:col-span-2">
            <label for="bidang_keahlian" class="mb-2 block text-sm font-bold text-slate-700">Bidang Keahlian</label>
            <input id="bidang_keahlian" name="bidang_keahlian" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" value="{{ old('bidang_keahlian', $user->bidang_keahlian) }}" placeholder="Contoh: Tata kelola SPBE, keamanan informasi, arsitektur enterprise">
            <x-input-error class="mt-2" :messages="$errors->get('bidang_keahlian')" />
        </div>

        <div class="sm:col-span-2">
            <label for="bio" class="mb-2 block text-sm font-bold text-slate-700">Bio Singkat</label>
            <textarea id="bio" name="bio" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Tuliskan fokus pekerjaan atau kontribusi Anda pada SPBE.">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            <p class="mt-2 text-xs text-slate-400">Data ini merupakan profil kolaborasi portal, bukan pengganti data induk SIASN/MyASN.</p>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 sm:col-span-2">
                Email belum terverifikasi.
                <button form="send-verification" class="font-bold underline">Kirim ulang email verifikasi</button>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-semibold text-emerald-700">Tautan verifikasi baru telah dikirim.</p>
                @endif
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-4 sm:col-span-2">
            <button type="submit" class="rounded-xl px-5 py-3 text-sm font-bold text-white" style="background-color: var(--portal-primary);">Simpan Profil</button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-700">Profil berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
