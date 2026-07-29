<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artikel;
use App\Models\Regulasi;
use App\Models\ForumTopik;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Anda sudah menjalankan UserSeeder dari langkah sebelumnya 
        // sehingga user dengan ID 1 (Admin) dan ID 2 (ASN) sudah ada.

        // 1. Contoh Data Regulasi
        Regulasi::updateOrCreate(
            [
                'nomor' => 'Perpres No. 95',
                'tahun' => 2018,
            ],
            [
                'tentang' => 'Sistem Pemerintahan Berbasis Elektronik (SPBE)',
                'file_path' => 'regulasi/perpres-95-2018.pdf',
            ]
        );

        // 2. Contoh Data Artikel (Satu disetujui, satu masih pending)
        Artikel::updateOrCreate(
            [
                'user_id' => 2,  // Dibuat oleh ASN
                'judul' => 'Penerapan KMS di Lingkungan Instansi Pemerintah',
            ],
            [
                'konten' => 'Knowledge Management System (KMS) sangat penting untuk pengelolaan pengetahuan aparatur sipil negara...',
                'status' => 'approved', // Sudah disetujui admin, akan muncul di beranda/publik
            ]
        );

        Artikel::updateOrCreate(
            [
                'user_id' => 2,  // Dibuat oleh ASN
                'judul' => 'Cara Meningkatkan Indeks SPBE Kota',
            ],
            [
                'konten' => 'Berikut adalah poin penting yang harus disiapkan untuk meningkatkan penilaian indeks SPBE...',
                'status' => 'pending',  // Masih menunggu validasi, hanya muncul di dashboard admin
            ]
        );

        // 3. Contoh Data Forum Diskusi
        ForumTopik::updateOrCreate(
            [
                'user_id' => 2,
                'judul_topik' => 'Bagaimana integrasi Pusdatin dengan sistem lokal?',
            ],
            [
                'deskripsi' => 'Rekan-rekan ASN, ada yang tahu kendala teknis saat sinkronisasi API data pegawai?'
            ]
        );
    }
}
