<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regulasis', function (Blueprint $table) {
        $table->id();
        $table->string('nomor'); // Misal: No 95
        $table->integer('tahun'); // Misal: 2018
        $table->string('tentang'); // Misal: Sistem Pemerintahan Berbasis Elektronik
        $table->string('file_path')->nullable(); // Tempat menyimpan file PDF regulasi
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulasis');
    }
};
