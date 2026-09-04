<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed akun admin default.
     * Login: admin@pkl.test / password
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@pkl.test'],
            [
                'name' => 'Admin PKL',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
