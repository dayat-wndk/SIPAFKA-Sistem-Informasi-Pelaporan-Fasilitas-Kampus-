<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin FMIPA',
                'email' => 'admin.fmipa@uho.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'aktif' => true,
            ],
            [
                'name' => 'Nadia Putri',
                'email' => 'nadia@uho.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pelapor',
                'aktif' => true,
            ],
            [
                'name' => 'Rizal Hidayat',
                'email' => 'rizal@uho.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pelapor',
                'aktif' => true,
            ],
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@uho.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'teknisi',
                'aktif' => true,
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti@uho.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'teknisi',
                'aktif' => true,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
