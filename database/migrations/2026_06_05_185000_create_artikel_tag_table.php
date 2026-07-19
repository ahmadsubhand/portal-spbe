<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('artikel_tag')) {
            Schema::create('artikel_tag', function (Blueprint $table) {
                $table->foreignId('artikel_id')->constrained('artikels')->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
                $table->primary(['artikel_id', 'tag_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel_tag');
    }
};
