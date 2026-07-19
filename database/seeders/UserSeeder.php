<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['email' => 'admin@spbe.test'],
            [
                'name' => 'Admin SPBE',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'asn@spbe.test'],
            [
                'name' => 'ASN SPBE',
                'password' => Hash::make('password'),
                'role' => 'asn',
                'is_active' => true,
            ]
        );
    }
}
