<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\RfidCard;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Data Mahasiswa Dummy Polinela 2026 diselaraskan dengan Jurusan, Prodi, dan Kelas resmi.
     */
    public function run(): void
    {
        $participants = [
            // ==========================================
            // 1. Jurusan Teknologi Informasi (JTI)
            // ==========================================
            // Prodi: Manajemen Informatika (MI) - Kelas: MI 3A (2024)
            [
                'identifier' => '24753001',
                'name' => 'Ahmad Fauzi',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'MI 3A',
                'cohort_year' => '2024',
                'gender' => 'L',
                'phone' => '081234567801',
                'card_uid' => 'E2000019',
            ],
            [
                'identifier' => '24753002',
                'name' => 'Annisa Permata Putri',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'MI 3A',
                'cohort_year' => '2024',
                'gender' => 'P',
                'phone' => '081234567802',
                'card_uid' => 'E2000020',
            ],
            [
                'identifier' => '24753003',
                'name' => 'Hendra Setiawan',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'MI 3A',
                'cohort_year' => '2024',
                'gender' => 'L',
                'phone' => '081234567809',
                'card_uid' => 'E2000027',
            ],

            // Prodi: Teknologi Rekayasa Perangkat Lunak (TRPL) - Kelas: TRPL 1B (2025)
            [
                'identifier' => '25753004',
                'name' => 'Bayu Pratama',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'TRPL 1B',
                'cohort_year' => '2025',
                'gender' => 'L',
                'phone' => '081234567803',
                'card_uid' => 'E2000021',
            ],
            [
                'identifier' => '25753005',
                'name' => 'Citra Dewi Lestari',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'TRPL 1B',
                'cohort_year' => '2025',
                'gender' => 'P',
                'phone' => '081234567804',
                'card_uid' => 'E2000022',
            ],
            [
                'identifier' => '25753006',
                'name' => 'Dimas Arya Putra',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'TRPL 1B',
                'cohort_year' => '2025',
                'gender' => 'L',
                'phone' => '081234567805',
                'card_uid' => 'E2000023',
            ],

            // ==========================================
            // 2. Jurusan Ekonomi dan Bisnis (JEB)
            // ==========================================
            // Prodi: Akuntansi Perpajakan - Kelas: AKT 4B (2023)
            [
                'identifier' => '23755001',
                'name' => 'Gita Gutawa Putri',
                'department' => 'Ekonomi dan Bisnis',
                'study_program' => 'Akuntansi Perpajakan',
                'class_group' => 'AKT 4B',
                'cohort_year' => '2023',
                'gender' => 'P',
                'phone' => '081234567808',
                'card_uid' => 'E2000026',
            ],
            // Prodi: Agribisnis Pangan - Kelas: AGB 2A (2024)
            [
                'identifier' => '24754002',
                'name' => 'Fajar Hidayat',
                'department' => 'Ekonomi dan Bisnis',
                'study_program' => 'Agribisnis Pangan',
                'class_group' => 'AGB 2A',
                'cohort_year' => '2024',
                'gender' => 'L',
                'phone' => '081234567807',
                'card_uid' => 'E2000025',
            ],

            // ==========================================
            // 3. Jurusan Teknik (JT)
            // ==========================================
            // Prodi: Teknologi Rekayasa Konstruksi Jalan dan Jembatan (TRKJJ)
            [
                'identifier' => '24735001',
                'name' => 'Rizki Ramadhan',
                'department' => 'Teknik',
                'study_program' => 'Teknologi Rekayasa Konstruksi Jalan dan Jembatan',
                'class_group' => 'TRKJJ 3A',
                'cohort_year' => '2024',
                'gender' => 'L',
                'phone' => '081234567810',
                'card_uid' => 'E2000028',
            ],
            // Prodi: Teknologi Rekayasa Otomotif (TRO)
            [
                'identifier' => '25735002',
                'name' => 'Muhammad Danu',
                'department' => 'Teknik',
                'study_program' => 'Teknologi Rekayasa Otomotif',
                'class_group' => 'TRO 1A',
                'cohort_year' => '2025',
                'gender' => 'L',
                'phone' => '081234567811',
                'card_uid' => 'E2000029',
            ],
        ];

        foreach ($participants as $p) {
            $cardUid = $p['card_uid'] ?? null;
            $isActive = $p['is_active'] ?? true;
            unset($p['card_uid'], $p['is_active']);

            $participant = Participant::updateOrCreate(
                ['identifier' => $p['identifier']],
                array_merge($p, ['is_active' => $isActive])
            );

            if ($cardUid) {
                RfidCard::updateOrCreate(
                    ['participant_id' => $participant->id, 'card_uid' => $cardUid],
                    [
                        'is_active' => true,
                        'issued_at' => now(),
                        'notes' => 'Penerbitan kartu perdana mahasiswa',
                    ]
                );
            }
        }
    }
}
