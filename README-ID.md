# Portal Pengetahuan SPBE Kota Bogor

Portal Pengetahuan Sistem Pemerintahan Berbasis Elektronik (SPBE) Kota Bogor adalah platform berbasis web yang dirancang untuk mengelola artikel publikasi, diskusi komunitas (forum), basis pengetahuan, regulasi, dan dashboard indikator SPBE. 

Aplikasi ini dibangun menggunakan framework **Laravel** dengan struktur monorepo, tipis di sisi Controller (*thin controllers*), serta memusatkan logika bisnis pada layanan (*services*) dan otorisasi menggunakan *Policies*.

---

## 🛠️ Tech Stack & Dependencies

- **Core Framework:** Laravel 11+ / 13+
- **Language:** PHP 8.3+
- **Database:** SQLite (Default Dev) / MySQL / PostgreSQL (Production)
- **Frontend Engine:** Blade Templates, Tailwind CSS (via Vite), Alpine.js, Quill Rich Text Editor.
- **Admin Panel:** Filament (Preferred) / Laravel Custom Admin Panel.

---

## 👥 Hak Akses & Peran Pengguna

| Peran | Deskripsi Hak Akses |
|---|---|
| **Guest (Publik)** | Melihat & mencari artikel yang sudah terbit, melihat topik forum terbuka (akses baca terbatas). |
| **ASN** | Membuat draft artikel, mengajukan peninjauan, mengelola topik forum sendiri, dan berpartisipasi aktif dalam diskusi. |
| **Admin** | Meninjau, menolak (wajib catatan), menyetujui, dan mempublikasikan artikel; mengelola pengguna, taksonomi (kategori & label), pengaturan sistem, dan memantau log audit. |

---

## 🔄 Alur Penulisan & Validasi Artikel

```
[ASN: Buat Draft] ➔ [ASN: Ajukan Validasi] ➔ [Admin: Setujui] ➔ [Admin: Terbitkan (Publik)]
                                           ↳ [Admin: Tolak] ➔ (Kembali ke Draft ASN dengan catatan)
```

---

## 🚀 Panduan Memulai (Setup Lokal)

Ikuti langkah-langkah di bawah ini untuk menjalankan portal di komputer lokal Anda:

### 1. Prasyarat
Pastikan Anda sudah menginstal:
- PHP >= 8.3 (beserta ekstensi php-sqlite, php-xml, php-mbstring, php-curl)
- Composer
- Node.js & NPM

### 2. Instalasi Otomatis (Setup Command)
Gunakan perintah pintas composer untuk mengunduh dependensi, menyalin `.env`, membuat kunci enkripsi, menjalankan migrasi database, dan membuild aset frontend:
```bash
composer run setup
```

### 3. Mengisi Data Awal (Database Seed)
Gunakan seeder bawaan untuk memuat data kategori, label, pengguna dummy (Admin, ASN, Guest), dan contoh artikel:
```bash
php artisan db:seed
```

### 4. Menjalankan Server Pengembangan
Gunakan perintah berikut untuk menjalankan server PHP dan compiler Vite secara bersamaan menggunakan utilitas `concurrently`:
```bash
npm run dev
```
Buka peramban (browser) di alamat: **`http://localhost:8000`**

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan uji fitur (*Feature Tests*) menggunakan PHPUnit untuk menjamin tidak adanya regresi keamanan maupun alur bisnis.
Untuk menjalankan seluruh 87 skenario uji, jalankan perintah:
```bash
php artisan test
```

---

## 📚 Struktur Dokumentasi (`docs/`)

Detail arsitektur, basis desain, dan keamanan dicatat di dalam folder [docs/](docs):
1. **[PRD.md](docs/PRD.md):** Persyaratan produk asli dan ruang lingkup MVP.
2. **[architecture.md](docs/architecture.md):** Penjelasan desain sistem, alur modul, dan desain antarmuka.
3. **[database-schema.md](docs/database-schema.md):** Struktur tabel database, relasi Eloquent, dan tipe data kolom.
4. **[deployment.md](docs/deployment.md):** Petunjuk langkah demi langkah peluncuran server produksi dan kompilasi aset.
5. **[security.md](docs/security.md):** Aturan dasar keamanan pengembangan, otorisasi, penanganan file, dan audit.
6. **[security-assessment.md](docs/security-assessment.md):** Hasil asesmen mandiri kesiapan rilis produksi berdasarkan OWASP Top 10.
