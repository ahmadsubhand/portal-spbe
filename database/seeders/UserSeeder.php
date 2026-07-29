<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@spbe.test')],
            [
                'name' => 'Admin SPBE',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => env('SEED_ASN_EMAIL', 'asn@spbe.test')],
            [
                'name' => 'ASN SPBE',
                'password' => Hash::make(env('SEED_ASN_PASSWORD', 'password')),
                'role' => 'asn',
                'is_active' => true,
            ]
        );

        // 5 Admin tambahan
        foreach (range(1, 5) as $i) {
            User::updateOrCreate(
                ['email' => "admin{$i}@spbe.test"],
                [
                    'name' => "Admin SPBE {$i}",
                    'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );
        }

        // 5 ASN tambahan
        foreach (range(1, 5) as $i) {
            User::updateOrCreate(
                ['email' => "asn{$i}@spbe.test"],
                [
                    'name' => "ASN SPBE {$i}",
                    'password' => Hash::make(env('SEED_ASN_PASSWORD', 'password')),
                    'role' => 'asn',
                    'is_active' => true,
                ]
            );
        }
    }
}
