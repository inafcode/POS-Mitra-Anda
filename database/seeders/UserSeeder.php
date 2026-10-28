<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mitraanda.com'],
            [
                'name' => 'Admin Mitra Anda & Kado Koeni',
                'password' => Hash::make('mitraanda321'), // Password default
            ]
        );
    }
}