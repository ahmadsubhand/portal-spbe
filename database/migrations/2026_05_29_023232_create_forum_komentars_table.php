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
        Schema::create('forum_komentars', function (Blueprint $table) {
        $table->id();
        // Menghubungkan komentar ke topik diskusi yang mana
        $table->foreignId('forum_topik_id')->constrained()->onDelete('cascade');
        // Menghubungkan ke siapa yang berkomentar
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('isi_komentar');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_komentars');
    }
};
