<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Akun Administrator & Timdis Apel Polinela 2026.
     */
    public function run(): void
    {
        // 1. Admin Utama
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

        // 2. Akun Timdis A: Dosen Pembina Jurusan Teknologi Informasi (Prodi Manajemen Informatika, Kelas MI 3A)
        User::updateOrCreate(
            ['username' => 'timdis_mi'],
            [
                'name' => 'Dr. Hendro Prasetyo, M.Kom (Timdis MI)',
                'email' => 'hendro.timdis@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'MI 3A',
                'cohort_year' => '2024',
                'is_active' => true,
            ]
        );

        // 3. Akun Timdis B: Dosen Pembina Jurusan Teknologi Informasi (Prodi TRPL, Kelas TRPL 1B)
        User::updateOrCreate(
            ['username' => 'timdis_trpl'],
            [
                'name' => 'Budi Santoso, S.Kom., M.T (Timdis TRPL)',
                'email' => 'budi.timdis@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'TRPL 1B',
                'cohort_year' => '2025',
                'is_active' => true,
            ]
        );

        // 4. Akun Timdis C: Dosen Pembina Jurusan Ekonomi dan Bisnis (Prodi Akuntansi Perpajakan)
        User::updateOrCreate(
            ['username' => 'timdis_akt'],
            [
                'name' => 'Dra. Marlinda, M.P (Timdis EKBIS)',
                'email' => 'marlinda.timdis@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'department' => 'Ekonomi dan Bisnis',
                'study_program' => 'Akuntansi Perpajakan',
                'class_group' => 'AKT 4B',
                'cohort_year' => '2023',
                'is_active' => true,
            ]
        );

        // 5. Akun Timdis Umum (Koordinator Apel Kampus Tanpa Pembatasan Jurusan)
        User::updateOrCreate(
            ['username' => 'timdis1'],
            [
                'name' => 'Siti Rahmawati, S.P., M.Si (Timdis Umum)',
                'email' => 'siti.timdis@polinela.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'timdis',
                'department' => null,
                'study_program' => null,
                'class_group' => null,
                'cohort_year' => null,
                'is_active' => true,
            ]
        );
    }
}
