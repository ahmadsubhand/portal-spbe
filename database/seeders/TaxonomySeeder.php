<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Manajemen SPBE' => 'Perencanaan, koordinasi, dan pengelolaan SPBE secara menyeluruh.',
            'Audit TIK' => 'Audit teknologi informasi dan komunikasi dalam penyelenggaraan SPBE.',
            'Infrastruktur SPBE' => 'Pusat data, jaringan intra, sistem penghubung layanan, dan infrastruktur pendukung.',
            'Aplikasi SPBE' => 'Pembangunan, pengembangan, integrasi, dan pengelolaan aplikasi SPBE.',
            'Layanan SPBE' => 'Penyelenggaraan dan peningkatan kualitas layanan pemerintahan berbasis elektronik.',
            'Tata Kelola SPBE' => 'Kebijakan, proses bisnis, arsitektur, dan tata kelola penyelenggaraan SPBE.',
            'Pemantauan dan Evaluasi SPBE' => 'Pemantauan, evaluasi, pengukuran indeks, dan tindak lanjut SPBE.',
            'Penguatan Kebijakan SPBE' => 'Penyusunan dan penguatan kebijakan internal maupun lintas instansi.',
            'Penyelenggara SPBE' => 'Peran, koordinasi, kompetensi, dan kelembagaan penyelenggara SPBE.',
            'Keamanan Informasi' => 'Manajemen risiko, perlindungan data, dan keamanan informasi SPBE.',
        ];

        foreach ($categories as $name => $description) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description],
            );
        }

        $labels = [
            'Manajemen Pengetahuan',
            'Arsitektur SPBE',
            'Tim Koordinasi SPBE',
            'Peta Rencana SPBE',
            'Manajemen Data',
            'Pembangunan Aplikasi',
            'Layanan Pusat Data',
            'Jaringan Intra',
            'Sistem Penghubung Layanan',
            'Transformasi Digital',
        ];

        foreach ($labels as $name) {
            Tag::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
