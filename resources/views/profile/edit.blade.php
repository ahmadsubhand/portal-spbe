@if ($user->isAdmin())
    <x-admin-layout title="Profil Saya" subtitle="Kelola identitas akun, keamanan password, dan preferensi akses pribadi.">
        <x-profile-settings :user="$user" :document-types="$documentTypes" />
    </x-admin-layout>
@else
    <x-asn-layout title="Profil Saya" subtitle="Kelola identitas kedinasan, foto profil, dan keamanan akun Anda.">
        <x-profile-settings :user="$user" :document-types="$documentTypes" />
    </x-asn-layout>
@endif
