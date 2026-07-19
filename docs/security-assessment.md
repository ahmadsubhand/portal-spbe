# Laporan Asesmen Keamanan (OWASP Top 10) - Portal SPBE

Dokumen ini memuat hasil asesmen keamanan mandiri untuk **Portal Pengetahuan SPBE Kota Bogor** berdasarkan standar industri **OWASP Top 10 (2021)**. Asesmen ini bertujuan untuk mengevaluasi ketahanan aplikasi terhadap celah keamanan umum dan memastikan kesiapan sistem sebelum masuk ke tahap produksi.

---

## Ringkasan Eksekutif

Aplikasi Portal SPBE dibangun dengan Laravel dan mengikuti praktik arsitektur modular yang solid. Berdasarkan pengujian statis dan dinamis (melalui *Feature Tests* otomatis sebanyak 87 skenario uji), aplikasi ini memiliki **tingkat keamanan yang tinggi (High Security Posture)**. Sistem otorisasi telah tersentralisasi menggunakan Laravel Policies, dan seluruh jalur input penulisan dilindungi oleh Form Requests yang membatasi parameter secara ketat.

---

## Analisis Berdasarkan OWASP Top 10 (2021)

### 🛡️ A01: Broken Access Control (Kontrol Akses Rusak)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Pembatasan rute administratif (`/admin/*`) dikunci menggunakan middleware peran custom `role:admin`. Fitur internal ASN diproteksi dengan `role:admin,asn`.
  - Otorisasi di tingkat record (horizontal) diatur melalui Laravel Policies:
    - [ArtikelPolicy](../app/Policies/ArtikelPolicy.php) memastikan ASN hanya bisa melihat/mengedit draft artikel miliknya sendiri. ASN tidak memiliki izin untuk mempublikasikan artikel langsung tanpa persetujuan Admin.
    - [ProfileDocumentPolicy](../app/Policies/ProfileDocumentPolicy.php) membatasi pengunduhan dan penghapusan dokumen profil hanya untuk pemilik dokumen tersebut.
  - Untuk forum diskusi, topik bertutup (*closed topic*) tidak dapat diakses oleh Guest (pengguna tanpa login). Kueri filter diselaraskan dan detail topik tertutup dialihkan secara aman ke halaman login.

### 🔑 A02: Cryptographic Failures (Kegagalan Kriptografi)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Sandi akun pengguna di-hash secara otomatis menggunakan algoritme **Bcrypt** berstandar industri.
  - Sesi aplikasi dienkripsi menggunakan kunci enkripsi simetris AES-256-CBC berbasis variabel lingkungan `APP_KEY` yang tersimpan di `.env`.
  - Serialisasi data sesi diubah menjadi format `json` (ditetapkan pada [session.php](../config/session.php#L231)) untuk menghindari serangan deserialisasi objek PHP (*PHP Object Injection*).

### 💉 A03: Injection (Injeksi)
- **Status:** **AMAN & DIKERASKAN**
- **Temuan & Implementasi:**
  - **SQL Injection:** Aplikasi tidak menggunakan query SQL mentah (`DB::raw` atau query manual) dengan input langsung. Interaksi database menggunakan Eloquent ORM secara penuh yang memanfaatkan parameter binding secara otomatis.
  - **Cross-Site Scripting (XSS):**
    - Input Rich Text (seperti artikel dan konten forum) disaring menggunakan [RichContentSanitizer](../app/Support/RichContentSanitizer.php) berbasis `DOMDocument` PHP untuk membersihkan tag berbahaya seperti `<script>`, `<style>`, `<embed>`, dan atribut JavaScript inline (`on*`).
    - Input teks biasa (seperti balasan forum) dirender menggunakan kurung kurawal ganda Blade (`{{ $reply->content }}`) yang secara otomatis meloloskan (*escape*) karakter HTML khusus.
  - **Pengerasan Validasi Admin Settings (Baru):** Menambahkan aturan validasi ketat pada [UpdateSettingsRequest](../app/Http/Requests/UpdateSettingsRequest.php) untuk mencegah parameter injection pada nilai pengaturan admin (misalnya memastikan URL dashboard publik hanya mengarah ke domain Google Data Studio/Looker Studio yang tepercaya, dan email publik berformat email valid).

### 📐 A04: Insecure Design (Desain Tidak Aman)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Penonaktifan akun dikonfigurasi melalui alur pengajuan persetujuan ke Admin. Data artikel dan forum dari pengguna yang dinonaktifkan tetap dipertahankan untuk menjamin integritas data instansi.
  - Akun admin tidak dapat dinonaktifkan atau diturunkan perannya (*demoted*) oleh dirinya sendiri demi mencegah admin terkunci keluar dari sistem secara tidak sengaja.

### ⚙️ A05: Security Misconfiguration (Salah Konfigurasi Keamanan)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Debug mode dikendalikan melalui `.env`. `APP_DEBUG=true` hanya diaktifkan di tahap lokal. Pada server staging dan produksi, opsi ini disetel ke `false`.
  - Generator URL avatar profil diubah menjadi `asset('storage/...')` untuk mendeteksi port aktif server secara dinamis, mencegah kegagalan akses gambar ketika dijalankan pada port non-standar (misalnya port 8000).

### 📦 A06: Vulnerable and Outdated Components (Komponen Rentan & Kedaluwarsa)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Menggunakan framework Laravel `^13.8`/`11+` dan PHP `^8.3` yang mendapat dukungan patch keamanan aktif.
  - Paket-paket npm eksternal minimal (hanya Tailwind CSS, Alpine.js, Quill, dan Vite) untuk memperkecil permukaan serangan (*attack surface*).

### 👥 A07: Identification and Authentication Failures (Kegagalan Autentikasi)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Sistem dilengkapi dengan pembatasan percobaan masuk (*login throttling*) maksimal 5 kali per kombinasi alamat IP dan email pengguna sebelum diblokir sementara (*cool-off* selama 60 detik) untuk mengantisipasi serangan brute force.
  - Halaman pendaftaran mandiri (register) dibatasi hanya berupa antarmuka statis informasi permohonan akun ke pengelola. Rute backend pendaftaran ditutup untuk umum sehingga tidak ada pembuatan akun otomatis tanpa persetujuan Admin.

### 🔄 A08: Software and Data Integrity Failures (Kegagalan Integritas Perangkat Lunak)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Unggahan dokumen profil pengguna divalidasi dengan membatasi tipe berkas (`pdf`, `jpg`, `jpeg`, `png`) dan ukuran berkas maksimal 5 MB.
  - Berkas sensitif seperti dokumen kelengkapan administrasi disimpan di folder privat (`storage/app/profile-documents/`) yang tidak dapat diakses langsung oleh publik. Pengunduhan dokumen diatur melalui [ProfileController::downloadDocument](../app/Http/Controllers/ProfileController.php#L94) dengan pengecekan kebijakan otorisasi terlebih dahulu.

### 📝 A09: Security Logging and Monitoring Failures (Kegagalan Log Keamanan)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Penggunaan `AuditLogService` secara konsisten mencatat operasi manajemen data penting (misal: pendaftaran pengguna baru, pembuatan/penghapusan dokumen, publikasi artikel, pengubahan settings admin, pengajuan penonaktifan akun) beserta pelakunya ke dalam tabel `audit_logs` di database.

### 🌐 A10: Server-Side Request Forgery (SSRF)
- **Status:** **AMAN**
- **Temuan & Implementasi:**
  - Aplikasi tidak memuat fungsi untuk melakukan HTTP request eksternal ke luar server berdasarkan masukan URL dari pengguna. Integrasi Looker Studio dimuat secara client-side via *iframe* dengan sandboxing aman.

---

## Checklist Kelayakan Produksi (Go-Live Checklist)

Sebelum meluncurkan aplikasi ini ke server produksi (hosting/VPS), pastikan hal-hari berikut telah diatur pada berkas `.env` server produksi:

```env
# 1. Matikan mode debug aplikasi
APP_ENV=production
APP_DEBUG=false

# 2. Hasilkan kunci enkripsi aplikasi yang baru dan aman
# (Jalankan command: php artisan key:generate)
APP_KEY=base64:...

# 3. Amankan cookie sesi melalui HTTPS (wajib menggunakan sertifikat SSL/HTTPS di server)
SESSION_SECURE_COOKIE=true

# 4. Batasi akses berkas konfigurasi dengan mengunci session cookie
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```
