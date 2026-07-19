<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            $table->boolean('is_draft')->default(true);
            $table->text('review_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            $table->dropColumn([
                'is_draft',
                'review_note',
                'submitted_at',
                'approved_at',
                'published_at',
            ]);
        });
    }
};
