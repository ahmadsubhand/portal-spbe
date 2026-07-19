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
        Schema::create('artikels', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke tabel users (siapa penulisnya)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('judul');
        $table->text('konten');
        // Kolom status sesuai kebutuhan Anda dengan nilai default 'pending'
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
