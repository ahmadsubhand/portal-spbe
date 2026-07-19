<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email');
            $table->string('nip', 18)->nullable()->unique()->after('avatar_path');
            $table->string('phone_number', 30)->nullable()->after('nip');
            $table->string('unit_kerja')->nullable()->after('phone_number');
            $table->string('jabatan')->nullable()->after('unit_kerja');
            $table->string('bidang_keahlian', 500)->nullable()->after('jabatan');
            $table->text('bio')->nullable()->after('bidang_keahlian');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn([
                'avatar_path',
                'nip',
                'phone_number',
                'unit_kerja',
                'jabatan',
                'bidang_keahlian',
                'bio',
            ]);
        });
    }
};
