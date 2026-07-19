<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Str;

class ForumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama sebagai contoh pembuat topik (pastikan Anda sudah punya minimal 1 user)
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $topics = [
            [
                'title' => 'Implementasi Peraturan Presiden Nomor 132 Tahun 2022 di Lingkungan Pemkot',
                'category' => 'Regulasi',
                'content' => 'Selamat siang rekan-rekan, bagaimana progress penyusunan Arsitektur SPBE di dinas masing-masing sesuai dengan amanat Perpres 132/2022? Apakah ada kendala dalam menyelaraskan proses bisnis lokal dengan arsitektur nasional? Mohon sharing-nya.',
                'views_count' => 142
            ],
            [
                'title' => 'Panduan Penggunaan Sistem Manajemen Pengetahuan SPBE untuk Pegawai Baru',
                'category' => 'Basis Pengetahuan',
                'content' => 'Halo semua, bagi rekan-rekan ASN yang baru bergabung, tim Diskominfo telah merilis modul video panduan pengoperasian aplikasi ini. Silakan akses menu dokumen untuk mengunduh PDF panduan cepat agar mempermudah input data riset dan inovasi.',
                'views_count' => 95
            ],
            [
                'title' => 'Troubleshooting Akses API Integrasi Data Layanan Publik di Dashboard Bogor Single Window',
                'category' => 'Teknis',
                'content' => 'Rekan-rekan tim teknis, sejak pembaruan security patch minggu lalu, beberapa endpoint API dari Dinas Kependudukan mengalami timeout saat di-hit oleh middleware dashboard utama. Apakah ada yang mengalami hal serupa? Bagaimana konfigurasi CORS terbaru di server kalian?',
                'views_count' => 210
            ],
            [
                'title' => 'Sharing Best Practice Pengelolaan Pengaduan Masyarakat Lewat SiBadra',
                'category' => 'Umum',
                'content' => 'Izin berbagi pengalaman dari Kecamatan Bogor Timur dalam mempercepat SLA respons aduan warga di aplikasi SiBadra. Kami membentuk tim taktis internal khusus yang terintegrasi langsung dengan seksi sarana prasarana, sehingga verifikasi lapangan bisa selesai dalam waktu < 2 jam.',
                'views_count' => 184
            ],
            [
                'title' => 'Rencana Migrasi Data Pusat Data Nasional (PDN) ke Cluster Lokal',
                'category' => 'Teknis',
                'content' => 'Menindaklanjuti arahan mengenai kedaulatan data infrastruktur SPBE, apakah dinas kalian sudah mulai mendata server *legacy* yang akan dimigrasikan tahun ini? Apa saja parameter backup yang wajib disiapkan agar tidak terjadi *downtime* pada sistem pelayanan aktif?',
                'views_count' => 64
            ],
            [
                'title' => 'Evaluasi Indeks Kematangan SPBE Kota Bogor Tahun Ini',
                'category' => 'Regulasi',
                'content' => 'Mari kita bedah bersama poin-poin penilaian mandiri terkait indeks SPBE kita. Sektor mana yang menurut rekan-rekan masih perlu dongkrak nilai secara masif? Apakah di domain kebijakan, tata kelola, atau manajemen layanan?',
                'views_count' => 115
            ]
        ];

        foreach ($topics as $t) {
            Topic::create([
                'user_id' => $userId,
                'title' => $t['title'],
                'slug' => Str::slug($t['title']),
                'content' => $t['content'],
                'category' => $t['category'],
                'views_count' => $t['views_count']
            ]);
        }
    }
}
