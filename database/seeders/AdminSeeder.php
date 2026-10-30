<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Utama
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Apel POLINELA',
                'email' => 'admin@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Timdis 1
        User::updateOrCreate(
            ['username' => 'timdis1'],
            [
                'name' => 'Hendro Prasetyo (Timdis)',
                'email' => 'timdis1@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'is_active' => true,
            ]
        );

        // Timdis Kompi A
        User::updateOrCreate(
            ['username' => 'petugas1'],
            [
                'name' => 'Budi Santoso (Timdis Kompi A)',
                'email' => 'petugas1@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'is_active' => true,
            ]
        );

        // Timdis Kompi B
        User::updateOrCreate(
            ['username' => 'petugas2'],
            [
                'name' => 'Siti Rahmawati (Timdis Kompi B)',
                'email' => 'petugas2@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'is_active' => true,
            ]
        );
    }
}
