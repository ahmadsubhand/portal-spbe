<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('artikels') && Schema::hasTable('categories') && ! Schema::hasColumn('artikels', 'category_id')) {
            Schema::table('artikels', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('user_id')->constrained('categories')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('artikels') && Schema::hasColumn('artikels', 'category_id')) {
            Schema::table('artikels', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }
    }
};
