<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table): void {
            $table->string('visibility')->default('open')->after('category');
        });

        Schema::table('replies', function (Blueprint $table): void {
            $table->foreignId('parent_id')->nullable()->after('topic_id')->constrained('replies')->cascadeOnDelete();
        });

        Schema::create('reply_likes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reply_id')->constrained('replies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['reply_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_likes');

        Schema::table('replies', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('topics', function (Blueprint $table): void {
            $table->dropColumn('visibility');
        });
    }
};
